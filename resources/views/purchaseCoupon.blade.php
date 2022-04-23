@extends('layouts.app')

@section('content')
    <div class="container-fluid my_flex_height"  >
        <div class="row my_flex_height"  >
            <coupons-purchase-form url="{{route('coupons.purchase.store')}}" class="col-xm-12 col-sm-6 col-md-7 col-lg-8"></coupons-purchase-form>
            <div id=statistics class="col-xm-12 col-sm-6 col-md-5 col-lg-4  ">
                <div id="statistic_food " class=" col-12 " >
                    <header>
                        <br/>
                        <h4>Στατιστικά γεύματος</h4>
                    </header>
                    <br/>
                    <br/>
                    <div class="row">
                        <label for="stoudents_koupons_numbers" class="col-9 col-lg-7">Κουπόνια Φοιτητών</label>
                        <label id="stoudents_koupons_numbers" name="stoudents_koupons_numbers"  class="col" ></label>
                    </div>
                </div>
                <div id="print_statistic_food " class=" col-12 ">
                    <header>
                        <br/>
                        <h4>Εξαγωγή στατιστικών</h4>
                    </header>
                    <br/>
                    <form action="myserver/php/statistic_export.php" target="_blank" method= "GET">
                        <div class="mx-auto"style=" min-width: 70%; max-width:80%;">
                            <select  onchange="adaptedCheck(this);" name="eidos_geymatos" id="eidos_geymatos" style="width: 100%;"  >
                                <option value="meal">Γεύμα</option>
                                <option value="today" >Σήμερα</option>
                                <option value="adapted" >Προσαρμοσμένο</option>
                            </select>
                            <div name="choose_meals" id="choose_meals" class="invisible">
                                <label class="checkbox-inline "><input type="checkbox" name="breakfast" value="breakfast" checked>Πρωινό</label>
                                <label class="checkbox-inline "><input type="checkbox" name="lunch" value="lunch" checked>Μεσημεριανό</label>
                                <label class="checkbox-inline "><input type="checkbox" name="dinner" value="dinner" checked>Βραδινό</label>
                            </div>
                        </div>
                        <div name="choose_days_period" id="choose_days_period" >
                            <label class=" ">Από:<input type="date" name="from_day" id="from_day" value=""></label>
                            <label class=" ">Μέχρι:<input type="date" name="to_day" id="to_day" value=""></label>
                        </div>
                        <input id="katigoria_xristi" name="katigoria_xristi" type="hidden" value="buy"/>
                        <button type="submit" class="  btn-primary" style="width: 100%" >Υποβολή</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

