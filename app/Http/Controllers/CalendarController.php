<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateCalendarEvents;
use Illuminate\Contracts\View\View;

/**
 * Class ReportController.
 */
class CalendarController extends BaseController
{
    /**
     * @return View
     */
    public function showCalendar()
    {
        $data = [
            'title' => trans('texts.calendar'),
            'company' => auth()->user()->company,
        ];

        return view('calendar', $data);
    }

    public function loadEvents()
    {
        if (auth()->user()->company->hasFeature(FEATURE_REPORTS)) {
            $events = dispatch_now(new GenerateCalendarEvents());
        } else {
            $events = [];
        }

        return response()->json($events);
    }
}
