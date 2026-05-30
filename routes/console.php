<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('abbd:reminder-feedback')->hourly();
