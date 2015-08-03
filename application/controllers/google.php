<?php

/**
 * Description of google
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\Registry as Registry;

class Google extends Controller {
    
    private $calendarId = 'hgjojsbcomh187bh6tiead5doc@group.calendar.google.com';

    private function configuration() {
        $configuration = Registry::get("configuration");
        $parsed = $configuration->parse("configuration/service");

        return $parsed;
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    protected function getClient() {
        $parsed = $this->configuration();
        $client = new Google_Client();
        $client->setApplicationName($parsed->api->google->application);
        $client->setScopes(implode(' ', array(
            Google_Service_Calendar::CALENDAR
        )));
        $client->setAuthConfigFile($parsed->api->google->secretfile);
        $client->setAccessType('offline');

        // Load previously authorized credentials from a file.
        $credentialsPath = $this->expandHomeDirectory($parsed->api->google->credentials);
        if (file_exists($credentialsPath)) {
            $accessToken = file_get_contents($credentialsPath);
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->authenticate($authCode);

            // Store the credentials to disk.
            if (!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, $accessToken);
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->refreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, $client->getAccessToken());
        }
        return $client;
    }

    /**
     * Expands the home directory alias '~' to the full path.
     * @param string $path the path to expand.
     * @return string the expanded path.
     */
    protected function expandHomeDirectory($path) {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }

    public function listEvents() {
        $this->noview();
        // Get the API client and construct the service object.
        $service = new Google_Service_Calendar($this->getClient());

        // Print the next 10 events on the user's calendar.
        $optParams = array(
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => TRUE,
            'timeMin' => date('c'),
        );
        $results = $service->events->listEvents($this->calendarId, $optParams);

        if (count($results->getItems()) == 0) {
            print "No upcoming events found.\n";
        } else {
            print "Upcoming events:\n";
            foreach ($results->getItems() as $event) {
                $start = $event->start->dateTime;
                if (empty($start)) {
                    $start = $event->start->date;
                }
                printf("%s (%s)\n", $event->getSummary(), $start);
            }
        }
    }

    public function createEvents($options) {
        $now = strftime("%Y-%m-%d", strtotime('now'));
        $service = new Google_Service_Calendar($this->getClient());
        
        $event = new Google_Service_Calendar_Event(array(
            'summary' => $options["summary"],
            'location' => $options["location"],
            'description' => $options["description"],
            'start' => array(
                'dateTime' => '2015-05-28T09:00:00-07:00',
                'timeZone' => 'Asia/Kolkata',
            ),
            'end' => array(
                'dateTime' => '2015-05-28T17:00:00-07:00',
                'timeZone' => 'Asia/Kolkata',
            ),
            'recurrence' => array(
                'RRULE:FREQ=DAILY;COUNT=2'
            ),
            'attendees' => array(
                array('email' => 'lpage@example.com'),
                array('email' => 'sbrin@example.com'),
            ),
            'reminders' => array(
                'useDefault' => FALSE,
                'overrides' => array(
                    array('method' => 'email', 'minutes' => 24 * 60),
                    array('method' => 'popup', 'minutes' => 10),
                ),
            ),
        ));

        return $service->events->insert($this->calendarId, $event);
    }

}
