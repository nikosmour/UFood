<?php

namespace Database\Seeders\Classes;

use App\Enum\UserAbilityEnum;
use App\Enum\UserStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


abstract class UserSeederPreparation extends Seeder
{
    use WithoutModelEvents;

    protected array $emailCounters;
    protected int $count;

    public function __construct($count = 10)
    {
        $this->count = $count;
        $this->emailCounters = collect(UserStatusEnum::cases())
            ->map(fn($status) => $status->name)
            ->sort()
            ->values()
            ->mapWithKeys(fn($name, $index) => [$name => ($index + 1) * 100000])
            ->toArray();
        foreach (UserStatusEnum::cases() as $status) {
            $key = $status->name;
            if ($this->whenInit($status))
                $this->initCounters($status);
            else
                unset($this->emailCounters[$key]);
        }
    }

    protected abstract function whenInit($status): bool;

    protected abstract function initCounters(UserStatusEnum $status): void;

    public function commonRun(array $seeders): void
    {
        echo str_pad('creating new users', 120, '.', STR_PAD_RIGHT);
        $startTime = microtime(true);
        $currentDay = Carbon::now()->subDay();

        $options = count($this->emailCounters);
        $count = intdiv($this->count, $options);
        foreach (array_keys($this->emailCounters) as $statusName) {
            $user_status = UserStatusEnum::enumByName()[$statusName];
            $this->createUser($count, $user_status);
        }
        $this->createUser($this->count - $count * $options);
        $endTime = microtime(true);
        $elapsedTime = $endTime - $startTime;
        echo str_pad(number_format($elapsedTime * 1000, 2), 7, ' ', STR_PAD_LEFT) . 'ms';
        echo "\033[0;32m" . ' DONE' . "\033[0m" . PHP_EOL;
        foreach ($seeders as $seeder) {
            echo str_pad(class_basename($seeder["class"]), 120, '.', STR_PAD_RIGHT);
            $startTime = $endTime;
            (new $seeder['class'](...$seeder['parameters']))->run();
//            $this->callWith($seeder['class'], [ 'count' => $seeder["count"]]);
            $endTime = microtime(true);
            $elapsedTime = $endTime - $startTime;
            echo str_pad(number_format($elapsedTime * 1000, 2), 7, ' ', STR_PAD_LEFT) . 'ms';
            echo "\033[0;32m" . ' DONE' . "\033[0m" . PHP_EOL;
        }
    }

    protected function generateEmail(string $status): string
    {
        $lower = strtolower($status);
        return "{$lower}{$this->emailCounters[$status]}@example.com";
    }

    protected function isAcademic($status): bool
    {
        return $status->canAny([
            UserAbilityEnum::CARD_OWNERSHIP,
            UserAbilityEnum::COUPON_OWNERSHIP
        ]);
    }


}