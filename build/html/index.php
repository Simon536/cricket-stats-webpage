<html>
    <head>
        <style>
        table, th, td {
            padding-left: 0px;
            padding-right: 20px;
        }
        h3 {
            text-align: center;
        }
        p {
            margin: 0px;
        }
        #columns {
            float: left;
            padding-right: 20px;
        }
        .red {
            color: red;
        }
        .status {
            text-transform: uppercase;
        }
        .name {
            font-weight: bold;
        }
        .name-detail {
            display: inline-flex;
            align-items: center;
        }
        .batting-indicator {
            margin-left: 5px;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background-color: red;
        }
        </style>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="refresh" content="120">
        <title>BBL 11</title>
    </head>

    <body>
        <div>
            <img src="img/heat.svg" width=72px>
            <img src="img/hurricanes.svg" width=72px>
            <img src="img/stars.svg" width=72px>
            <img src="img/thunder.svg" width=72px>
            <img src="img/sixers.svg" width=72px>
            <img src="img/renegades.svg" width=72px>
            <img src="img/strikers.svg" width=72px>
            <img src="img/scorchers.svg" width=72px>
        </div>

        <?php
            require 'vendor/autoload.php';
            $httpClient = new \GuzzleHttp\Client(['headers' => ['Referer' => 'https://duckduckgo.com/', 'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:95.0) Gecko/20100101 Firefox/95.0']]);

            libxml_use_internal_errors(true);
        
            $response = $httpClient->get('https://www.espncricinfo.com/live-cricket-score');
            $htmlString = (string) $response->getBody();
            $doc = new DOMDocument();
            $doc->loadHTML($htmlString);
            $xpath = new DOMXPath($doc);
            $live_scores = $xpath->evaluate('//div[@class="match-score-block"]/div[2]');
            echo "<table>";
            echo "<td>";
            $score_html = $doc->saveHTML($live_scores->item(0));
            $score_html = preg_replace('/<img [^<]*/', '', $score_html);   // Test regex expressions using https://www.phpliveregex.com
            $score_html = preg_replace('/ href=".*"/U', '', $score_html);  // U modifier ensures non-greedy matching
            echo $score_html;
            echo "</td>";
            echo "<td>";
            $score_html = $doc->saveHTML($live_scores->item(1));
            $score_html = preg_replace('/<img [^<]*/', '', $score_html);
            $score_html = preg_replace('/ href=".*"/U', '', $score_html);
            echo $score_html;
            echo "</td>";
            echo "<td>";
            $score_html = $doc->saveHTML($live_scores->item(2));
            $score_html = preg_replace('/<img [^<]*/', '', $score_html);
            $score_html = preg_replace('/ href=".*"/U', '', $score_html);
            echo $score_html;
            echo "</td>";
            echo "</table>";
        ?>

        <h1> BBL Stats 2021/2022 </h1>
        <div>
        <h2> Current Standings </h2>

        <table>
            <?php
                $response = $httpClient->get('https://www.espncricinfo.com/series/big-bash-league-2021-22-1269637/points-table-standings');
                $htmlString = (string) $response->getBody();

                libxml_use_internal_errors(true);

                $doc = new DOMDocument();
                $doc->loadHTML($htmlString);

                $xpath = new DOMXPath($doc);

                $teams = $xpath->evaluate('//div[@class="table-responsive"]//h5');
                $points = $xpath->evaluate('//div[@class="table-responsive"]//tr/td[7]');

                for ($x = 0; $x<8; $x++){
                    echo "<tr>";
                    echo "<td>";
                    switch ($teams[$x]->textContent) {
                        case "Melbourne Stars":
                            echo "<img src='img/stars.svg'>";
                            break;
                        case "Brisbane Heat":
                            echo "<img src='img/heat.svg'>";
                            break;
                        case "Hobart Hurricanes":
                            echo "<img src='img/hurricanes.svg'>";
                            break;
                        case "Sydney Thunder":
                            echo "<img src='img/thunder.svg'>";
                            break;
                        case "Sydney Sixers":
                            echo "<img src='img/sixers.svg'>";
                            break;
                        case "Melbourne Renegades":
                            echo "<img src='img/renegades.svg'>";
                            break;
                        case "Adelaide Strikers":
                            echo "<img src='img/strikers.svg'>";
                            break;
                        case "Perth Scorchers":
                            echo "<img src='img/scorchers.svg' height=48px>";
                            break;
                    }
                    echo "</td>";
                    echo "<td>";
                    echo $teams[$x]->textContent.PHP_EOL;
                    echo "</td>";
                    echo "<td><b>";
                    echo $points[$x]->textContent.PHP_EOL;
                    echo "</b></td>";
                    echo "</tr>";
                }
            ?>
        </table>
        </div>

        <?php

            function top10table($heading, $response, $itemNumber){
                $htmlString = (string) $response->getBody();
                $doc = new DOMDocument();
                $doc->loadHTML($htmlString);

                $xpath = new DOMXPath($doc);

                $names = $xpath->evaluate('//table[@class="engineTable"][1]//tr[@class="data2"][position() <= 10]/td[1]');
                $number = $xpath->evaluate('//table[@class="engineTable"][1]//tr[@class="data2"][position() <= 10]/td['.$itemNumber.']');
                $teams = $xpath->evaluate('//table[@class="engineTable"][1]//tr[@class="note"][position() <= 10]');

                echo "<div id=\"columns\">";
                echo "<h3>".$heading."</h3>";
                echo "<table>";

                for ($x = 0; $x<10; $x++) {
                    echo "<tr>";
                    echo "<td style='padding-right:0px'>";
                    echo ($x+1).".";
                    echo "</td>";
                    echo "<td>";
                    echo $names[$x]->textContent.PHP_EOL;
                    echo "</td>";
                    echo "<td>";
                    echo $teams[$x]->textContent.PHP_EOL;
                    echo "</td>";
                    echo "<td>";
                    echo $number[$x]->textContent.PHP_EOL;
                    echo "</td>";
                    echo "</tr>";
                }

                echo "</table>";
                echo "</div>";
            }

            // Most sixes
            $response = $httpClient->get('https://stats.espncricinfo.com/ci/engine/records/batting/most_sixes_career.html?id=14034;type=tournament');
            top10table("Sixes Hit", $response, 14);

            //Most runs
            $response = $httpClient->get('https://stats.espncricinfo.com/ci/engine/records/batting/most_runs_career.html?id=14034;type=tournament');
            top10table("Runs Scored", $response, 5);

            // Most wickets
            $response = $httpClient->get('https://stats.espncricinfo.com/ci/engine/records/bowling/most_wickets_career.html?id=14034;type=tournament');
            top10table("Wickets Taken", $response, 7);

            // Most Catches
            $response = $httpClient->get('https://stats.espncricinfo.com/ci/engine/records/fielding/most_catches_career.html?id=14034;type=tournament');
            top10table("Catches", $response, 4);
        ?>
    </body>
</html>
