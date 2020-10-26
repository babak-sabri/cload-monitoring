<?php
return [
	/*
    |--------------------------------------------------------------------------
    | Application Languages
    |--------------------------------------------------------------------------
    |
    | This value specifies the application supporded languages
    |
    */
	'languages'	=> [
		FARSI_LANGUAGE,
		ENGLISH_LANGUAGE
	],
	/*
    |--------------------------------------------------------------------------
    | Application Calendars
    |--------------------------------------------------------------------------
    |
    | This value specifies the application supporded calendars
    |
    */
	'calendars'	=> [
		JALALI_CALENDER,
		GREGORIAN_CALENDAR
	],

	/*
    |--------------------------------------------------------------------------
    | Application User Types
    |--------------------------------------------------------------------------
    |
    | This value specifies the application user type.
    | A user might be a normal user, tour leader , staff or a admin
    |
    */
	'user-types'	=> [
		ADMIN_USER,
		CUSTOMER_USER,
	],
	
	/*
    |--------------------------------------------------------------------------
    | Application User Genders
    |--------------------------------------------------------------------------
    |
    | This value specifies the application user genders.
    | A user might be a male or female
    |
    */
	'user-genders'	=> [
		MALE_GENDER,
		FEMALE_GENDER,
	],
	
	/*
    |--------------------------------------------------------------------------
    | User default timezone
    |--------------------------------------------------------------------------
    |
    | This value specifies the user default time zone
    |
    */
	'default-timezone'	=> env('USER_DEFAULT_TIMEZONE', 'Asia/Tehran'),
	
	/*
    |--------------------------------------------------------------------------
    | valid image mimes type
    |--------------------------------------------------------------------------
    |
    | This value specifies valid image mimes
    |
    */
	'image-mimes'	=> env('IMAGE_MIMES', 'jpeg,png,jpg,gif,svg'),
	
	/*
    |--------------------------------------------------------------------------
    | Icon max size
    |--------------------------------------------------------------------------
    |
    | This value specifies maximum icon size
    |
    */
	'icon-max-size'	=> env('ICON_MAX_SIZE', '1024'),
	
	/*
    |--------------------------------------------------------------------------
    | User Image Storage
    |--------------------------------------------------------------------------
    |
    | This value specifies user images storage
    |
    */
	'user-image-storage'	=> env('USER_IMAGE_STORAGE', 'users'),
];