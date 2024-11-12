<?php

namespace Tests\Feature;

use App\Models\Academic;
use Tests\TestCase;

class CouponTransactionPerformanceTest extends TestCase
{

    /**
     * Test the performance of couponTransactions with Academic vs CouponOwner.
     *
     * @return void
     */
    public function test_coupon_transactions_performance()
    {

        // Initialize the counters and dif array
        $win = [0, 0, 0]; // To store the counts for the different results (first < second, first > second, equal)
        $dif = []; // To store the differences in milliseconds

        // Loop through each academic instance
        $totalAcademics = Academic::count(); // Get the total number of academics
        $currentIndex = 0; // To keep track of the current iteration
        foreach (Academic::cursor() as $academic) {
            // Start the timer for the first code block
            $start = microtime(true);
            $academic->couponTransactions()->simplePaginate(10); // Access the couponTransactions relationship for the academic
            $end = microtime(true);
            $first = $end - $start; // Time taken for the first block

            // Start the timer for the second code block
            $start = microtime(true);
            $couponOwner = $academic->couponOwner; // Access the couponOwner relationship
            $couponOwner->couponTransactions()->simplePaginate(10); // Access couponTransactions of the couponOwner
            $end = microtime(true);
            $second = $end - $start; // Time taken for the second block

            // Update the win array based on the comparison between $first and $second
            if ($second > $first) {
                $win[0] += 1; // Academic took less time, so "win" for first
            } elseif ($second < $first) {
                $win[1] += 1; // CouponOwner took less time, so "win" for second
            } else {
                $win[2] += 1; // Equal times
            }

            // Store the difference between the two times in milliseconds
            $dif[] = ($first - $second) * 1000;
            $currentIndex++;
            if ($currentIndex % 200 === 0) {
                echo "Processed {$currentIndex} out of {$totalAcademics} academics...\n";
                ob_flush();  // Flush the output buffer
                flush();     // Flush the output
            }
        }
        // Calculate the average of the differences in milliseconds
        $averageDiff = count($dif) > 0 ? array_sum($dif) / count($dif) : 0;

        // Final message after the loop
        echo "Test completed! Here are the results:\n";

        // Output the results
        echo "CouponOwner was slower in {$win[0]} cases.\n";
        echo "Academic was slower in {$win[1]} cases.\n";
        echo "The times were equal in {$win[2]} cases.\n";
        echo "Average time (faster academics in milliseconds): " . number_format(-$averageDiff, 2) . " ms\n";
        echo "Max time difference  win of coupon (in milliseconds): " . number_format(max($dif), 2) . " ms\n";
        echo "Max time difference  win of academics (in milliseconds): " . number_format(-min($dif), 2) . " ms\n";

        // You can assert the results as per your expectations
        $this->assertGreaterThan(0, $win[0], 'Expected couponOwner to be slower in some cases');
        $this->assertGreaterThan(0, $win[1], 'Expected academic to be slower in some cases');
        $this->assertNotEmpty($dif, 'Expected some time differences to be recorded');
    }
}
