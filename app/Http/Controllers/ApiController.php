<?php

namespace App\Http\Controllers;

use App\Http\Requests\DateForRangeRequest;
use App\Http\Requests\DaysByRangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function daysByRange(DaysByRangeRequest $request)
    {
        $days = DB::table('workdays_ru')
            ->where('day', '>', $request->get('start'))
            ->where('day', '<=', $request->get('end'))
            ->where('is_work', 1)
            ->count();

        return $days;
    }

    public function dateForRange(DateForRangeRequest $request)
    {
        $dates = DB::table('workdays_ru')
            ->where('day', '>', $request->get('start'))
            ->where('is_work', 1)
            ->orderBy('day')
            ->limit($request->get('days'))
            ->pluck('day');

        return Arr::last($dates->toArray());
    }

    public function init()
    {
        $years = [
            '2021' => [],
            '2022' => [],
            '2023' => [],
            '2024' => [],
            '2025' => []
        ];

        foreach ($years as $key => $value) {
            $ch = curl_init();
            $url = 'http://isdayoff.ru/api/getdata?year=' . $key;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $str = curl_exec($ch);
            $years[$key] = str_split($str);
            curl_close($ch);
        }

        $workDays = [];

        foreach ($years as $key => $value) {

            $countDays = count($value);
            $startDay = Carbon::create($key);

            $workDays[$key] = [];
            $workDays[$key][] = [
                'day' => $startDay->toDateString(),
                'is_work' => $value[0] == 0 ? 1 : 0
            ];

            for ($i = 1; $i < $countDays; $i++) {
                $startDay->addDay();
                $workDays[$key][] = [
                    'day' => $startDay->toDateString(),
                    'is_work' => $value[$i] == 0 ? 1 : 0
                ];
            }
        }

        DB::table('workdays_ru')->delete();

        foreach ($workDays as $value) {
            DB::table('workdays_ru')->insert($value);
        }
    }
}
