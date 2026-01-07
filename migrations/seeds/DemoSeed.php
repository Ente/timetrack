<?php
use Phinx\Seed\AbstractSeed;

class DemoSeed extends AbstractSeed {
    public function run(): void {
        $users = [
            ['username' => 'demo_admin', 'password' => password_hash('demo123', PASSWORD_DEFAULT), 'email' => 'admin@example.com', 'active' => 1],
            ['username' => 'demo_user', 'password' => password_hash('demo123', PASSWORD_DEFAULT), 'email' => 'user@example.com', 'active' => 1],
        ];
        $this->insert('users', $users);

        $usernames = ['demo_admin', 'demo_user'];

        $types = ['normal', 'remote', 'training', 'meeting', 'vacation', 'sickness'];
        $locations = ['Berlin', 'Hamburg', 'Home Office', 'Munich'];
        $projects = ['Website Relaunch', 'Customer Portal', 'TimeTrack QA', 'Internal Review'];

        $today = new DateTimeImmutable('today');
        $entries = [];

        foreach ($usernames as $username) {
            for ($i = 0; $i < 10; $i++) {
                $date = $today->sub(new DateInterval("P{$i}D"))->format('Y-m-d');

                $startHour = rand(7, 9);
                $endHour = $startHour + 8;
                $pauseStart = sprintf("%02d:00", rand(12, 13));
                $pauseEnd = date('H:i', strtotime($pauseStart) + 1800); 

                $entries[] = [
                    'name'           => ucfirst(str_replace('_', ' ', $username)),
                    'email'          => "{$username}@example.com",
                    'schicht_tag'    => $date,
                    'schicht_anfang' => sprintf("%02d:00", $startHour),
                    'schicht_ende'   => sprintf("%02d:00", $endHour),
                    'username'       => $username,
                    'ort'            => $locations[array_rand($locations)],
                    'active'         => 1,
                    'review'         => (rand(0, 10) > 1) ? 1 : 0, 
                    'type'           => $types[array_rand($types)],
                    'pause_start'    => $pauseStart,
                    'pause_end'      => $pauseEnd,
                    'attachements'   => null,
                    'project'        => $projects[array_rand($projects)],
                ];
            }
        }

        $this->table('arbeitszeiten')->insert($entries)->saveData();

        echo "Inserted " . count($entries) . " demo worktime entries.\n";
    }
}
