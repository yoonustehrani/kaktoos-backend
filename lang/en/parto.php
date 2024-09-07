<?php

return [
    'web_service_error' => 'WebService Error',
    'errors' => [
        // search
        'Err0103001' => 'OriginDestinationInformations cannot be null',
        'Err0103002' => 'TravelPreferences cannot be null',
        'Err0103003' => 'Invalid AirTripType. OneWay cannot have more than one OriginDestinationInformation',
        'Err0103004' => 'OriginDestinationInformation cannot be null',
        'Err0103005' => 'OriginLocationCode cannot be null',
        'Err0103006' => 'DestinationLocationCode cannot be null',
        'Err0103007' => 'DepartureDateTime cannot be null',
        'Err0103009' => 'Unknown City/Airport',
        'Err0103010' => 'Unknown City/Airport',
        'Err0103011' => 'Travel date should be more than 24 hours before departure',
        'Err0103012' => 'Infant count should be less than Adults',
        'Err0103013' => 'Adults and Children together should not be more than 9',
        'Err0103014' => 'There should be atleast one Adult.',
        'Err0103016' => 'Flights not found for the given search condition',
        'Err0103017' => 'Please correct your travel date',
        'Err0103018' => 'Too many requested segments',
        'Err0103019' => 'Invalid CabinType',
        'Err0103020' => 'Invalid AirTripType',
        'Err0103021' => 'Date cannot be more than 350 days in the future',
        'Err0103022' => 'Unknown Origin Type',
        'Err0103023' => 'Unknown Destination Type',
        'Err0103024' => 'Unknown Airline (Preferred Airline)',
        'Err0103025' => 'Unknown Airline (Exclude Airline)',
        'Err0103026' => 'Origin And Destination Can Not Be Same!',
        // revalidate
        'Err0104001' => 'FareSourceCode cannot be null',
        'Err0104002' => 'Invalid FareSourceCode',
        'Err0104003' => 'Selected itinerary is no longer available. Please select different option.',
        'Err0104004' => 'For emergency issuance please call us',
        'Err0104005' => 'This flight is not valid anymore',
        'Err0104006' => 'This result is not valid anymore',
        'Err0104007' => 'Flight capacity is complete',
        // AirRules
        'Err0105001' => 'FareSourceCode or UniqueId cannot be null',
        'Err0105002' => 'Invalid FareSourceCode or UniqueId cannot be null',
        'Err0105003' => 'Fare rules not available',
        // Baggages
        'Err0106001' => 'FareSourceCode cannot be null',
        'Err0106002' => 'Invalid FareSourceCode',
        'Err0106003' => 'Baggages not available',
        // AirBook
        'Err0107001' => 'FareSourceCode cannot be null',
        'Err0107002' => 'Invalid FareSourceCode',
        'Err0107003' => 'TravelerInfo cannot be null',
        'Err0107004' => 'AreaCode cannot be null',
        'Err0107005' => 'CountryCode cannot be null',
        'Err0107006' => 'PhoneNumber cannot be null',
        'Err0107007' => 'AirTravelers cannot be null',
        'Err0107008' => 'Date of birth is not valid',
        'Err0107009' => 'PassengerName cannot be null',
        'Err0107010' => 'Passport cannot be null',
        'Err0107011' => 'Passport expiry date is not valid',
        'Err0107012' => 'Passport Country should be of format ^([A-Z][A-Z])$',
        'Err0107013' => "We're sorry, the fare for that itinerary is no longer available.",
        'Err0107014' => 'Exception...',
        'Err0107016' => "Sorry, There is a change in price",
        'Err0107018' => 'Invalid Data. Please check your data',
        'Err0107019' => 'Name (including Title) is too long',
        'Err0107020' => 'Please check your Passport details',
        'Err0107021' => 'Invalid National Code',
        'Err0107022' => 'PostalCode cannot be null',
        'Err0107023' => 'Booking already exists! Please check your booking list.',
        'Err0107024' => 'Only first passanger is allowed to add extra services',
        'Err0107025' => 'One or more segments are unserviceable.',
        'Err0107026' => 'Nationality should be of format ^([A-Z][A-Z])$',
        'Err0107027' => "Sorry, There is a change in time",
        // 'Err0107028' => n ADULT given while search but m ADULT information present in booking request
        // 'Err0107029' => n CHILD given while search but m CHILD information present in booking request
        // 'Err0107030' => n INFANT given while search but m INFANT information present in booking request
        // 'Err0107032' => There is duplicate booking for {0} under the booking no: {1}
        'Err0107033' => 'Duplicate Booking',
        'Err0107034' => 'Invalid Extra Service ID!',
        'Err0107035' => 'Children and infants must be accompanied by at least one adult',
        'Err0107036' => 'Traveller name is too long',
        'Err0107037' => 'Your required Flight has already been sold out. Please search for the latest price and book again',
        'Err0107038' => 'Invalid traveller nationality',
        'Err0107039' => 'Temporary supplier connection error',
        'Err0107040' => 'Passenger Passport Issued Country not present or invalid country code provided',
        'Err0107041' => 'Too few travellers',
        'Err0107042' => 'Specified luggage option is not available',
        'Err0107043' => 'Traveller type not supported',
        'Err0107044' => 'Booking failed by supplier',
        'Err0107045' => 'Passport issue date is not valid',
        'Err0107046' => 'Change Price',
        'Err0107047' => 'Invalid Passenger Title (For Adult-Male can be only MR)',
        'Err0107048' => 'Invalid Passenger Title (For Adult-Female can be only MRS',
        'Err0107049' => 'Invalid Passenger Title (For Child-Infant-Male can be only MSTR)',
        'Err0107050' => 'Invalid Passenger Title (For Child-Infant-Female can be only MISS)',
        'Err0107051' => 'Duplicate passport numbers detected in reservation',
        'Err0107052' => 'PassengerName is not valid',
        'Err0107053' => 'Mobile Number is not valid',
        'Err0107055' => 'Email cannot be null',
        'Err0107056' => 'Email is not valid',
        'Err0107057' => 'ClientUniqueId is not valid. There is already a booking with same ClientUniqueId.',
        'Err0107058' => 'There is a problem with the selected itinerary',
        'Err0107059' => 'The passenger is not eligible.',
        'Err0107060' => 'The Book is pending',
        'Err0107062' => 'Destination address is mandatory!',
        'Err0107063' => 'Not allowed to travel due to positive COVID-19 test result.',
        'Err0107064' => 'There is duplicate National ID',
        'Err0107065' => 'There is duplicate Passport Number',
        'Err0107066' => 'There is no time for ticketing arrangement from airline.',
        'Err0107067' => 'Invalid Meal Type ID!',
        // AirCancel
        'Err0108001' => 'UniqueID cannot be null',
        'Err0108002' => 'Invalid Booking Reference Number',
        'Err0108003' => 'Booking may be ticket or cancelled',
        'Err0108004' => 'Booking can not be cancelled',
        // AirBookingData
        'Err0109001' => 'UniqueID cannot be null',
        'Err0109002' => 'Invalid Booking Reference Number',
        'Err0109004' => 'Booking details not found for given Booking reference number',
        'Err0109005' => 'Secure PNR',
        'Err0109006' => 'UniqueID and ClientUniqueID cannot be both null',
    ]
];