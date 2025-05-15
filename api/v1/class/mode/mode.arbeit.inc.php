<?php
namespace Arbeitszeit {
    use Arbeitszeit\i18n;
    class Mode extends Arbeitszeit {
        public static function check($username){
            # 0 = normal mode
            # 1 = "easymode"
            $mode_value = Benutzer::get_user($username)["easymode"] ?? 0;

            if($mode_value == "0" || $mode_value == false){
                Exceptions::error_rep("Returning normal mode for user {$username}");
                return self::get_normal_mode_html();
            } else {
                Exceptions::error_rep("Returning easymode for user {$username}");
                return self::get_easymode_html();
            }
        }

        public static function compute_html_worktime_types(){
            $data = "";
            foreach(Arbeitszeit::get_all_types() as $type => $value){
                $data .= "<option value=\"{$type}\">{$value}</option>";
            }
            return $data;
        }


        private static function get_normal_mode_html(){
            $nodes = new Nodes;
            if($nodes->checkNode("mode.inc", "get_normal_mode_html") == false){
                return;
            }
            $i18n = new i18n;
            $loc = $i18n->loadLanguage(null, "mode/easymode");
            $t = self::compute_html_worktime_types();
            $data = <<< DATA
            <form action="actions/worktime/add.php" method="POST">
            <label name="ort">{$loc["loc"]}</label>
                <input class="input" type="text" name="ort" placeholder="{$loc["loc"]}">
                <br>
                <label name="date">{$loc["date"]}</label>
                <input class="input" type="date" name="date" data-date-format="DD.MM.YYYY" required>
                <br>
                <label name="schicht_beginn">{$loc["sbegin"]}</label>
                <input class="input" type="time" name="time_start" placeholder="{$loc["sbegin"]}" required>
                <br>
                <label name="schicht_ende">{$loc["send"]}</label>
                <input class="input" type="time" name="time_end" placeholder="{$loc["send"]}" required>
                <br>
                <label name="pause_start">{$loc["pbegin"]}</label>
                <input class="input" type="time" name="pause_start" placeholder="{$loc["pbegin"]}">
                <br>
                <label name="pause_end">{$loc["pend"]}</label>
                <input class="input" type="time" name="pause_end" placeholder="{$loc["pend"]}">
                <br>
                <label name="Wtype">{$loc["type"]}</label>
                <select class="input" name="Wtype">
                    {$t}
                </select>
                <br>
                <input class="input" type="text" name="username" value="{$_SESSION["username"]}" hidden>
                <button type="submit" class="button">{$loc["submit"]}</button>
            </form>

            <div class="box">
                <h3> <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAADqElEQVR4nO2ZW4hNYRTH95gxjGsSRcileJgHSpTbywzzIuVWLoUxyaVR8yKjyC0PpJRLqHlQ8iQvkhc8uCslhNxzCYNSIsO4/bTGf/PZZp9z9rnMnM351+7svc931rf+37p831rH8wr4hwGMAm7oGuXFDUARsBRo4jc+AfVABy8OAPoAxwjHCaCfl88AqoDGgOL3dbmwMVVevgEoBbYD36XoWeCd7qt1GT4A53X/Xb8p9fIBwBDgkpT7AmwEavT8ACgBioG7ercEqAM+6/kKMLy9SSwE3kuhx8CEgNLVzthqZ5xZcKyIoqRQ1x4EegCHHJ8/AvQKKNxiDec3LsGaZHLagsTYsJUMs0aYVUIs+wSYlEsCHZL5dpg1QoguDnw3QjINXxVrxdkmMRA442SbnUCnBEr+ZY1UyAIdReCbxlwChmaLxAzgjQS/BqZGVTAqYWAy8EJj3gLzMiFQppX3cQron65yUUkDfYHjzvwHga7pEPH3BhQXR4F9Mn0tMBOYaHGicxXawUtSkF3i7PbLJGOiZNZqDpvrqBOThovpELHdOd9wJjIRx736A6OBaUqT9XK5wzpu3JIfp4uPigeTc1IutFUZcqHipVx6FHntBV9bL+6gQCTPQMEifwLoAmzW3tOsT0vTZV5cLMLP7HkxJOtdaBMyWSKySWIeAZW2wyst2ynasCF7GueWyAOJqQi8NzKG+xkr2kZEmiWmW+B9d71vyljRbBLR4XM+cFoF1gXt7r5FKgPjp+j9zZwRcCZbYU2JFIq2ecDtkIBu1udjuVM3kfBjxHDH6plsKGwBuE09Kzt7HbBmnb6rSEBgjs5ZPh6q0zLMqkiHhNVCiXAllZN3MhJjnHrEha3YyBACs9UTxslIS4KrCuzW9+uA9SqDXdgizM2oFSufXuvUDNfUnBgu/0ZNhelOT9hqjuuOIk+B5WGNOmCLxp0LuJJhdca9ZGCwhPt1/A63jrd7HclRDb4LuOoo8QxYGaz9A3OUAy8DylssXNb9mqhKl8r//To6EzSq1uicZJEaAtWhue4CeYElhuiFlgqdTPEKWGVnpgTzDAL2OwFuuKcU/Ks1BPRWrBjRnlGIPG8RCeMirUC0dtPeZARcOGevWV6qcP4qGJ/yj1KTOwDYoz+BfFhTYlGyVMrPLGZoSDaJ/SETN5xrjUgs4YUR8WIC/hsicYPXChF/144TzkYyW/C5rUCmehSI5KtFgsi2ojnXg9YDP3Fg5QDkiR4FeGngB1llV2/2JzkbAAAAAElFTkSuQmCC"><br>{$loc["h_vacation"]}</h3>
                <p>{$loc["v_note"]}</p>
                <a href="/suite/worktime/vacation"><button class="button">{$loc["v_link"]}</button></a>
            </div>

            <div class="box">
                <h3><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAADw0lEQVR4nO2aW4hOURTHPyOXkjDuucRIqSkUwgvJJVIUU8rkEsLL5JKUB5oHiSfekBAlySiNGIwMQlPuuV+m3EeDmRqMu5+WWYfdmXMO33zrfN+Qf02dvfc667//395rn7X3nkTiP/5RAD2A2cA24AxQCdRQj0/AE+AosBTISTQ1AJO1g1/5c4jtXqB3UxCQA5wiNbwGxmRSxESgFhu8A4ZkQsQknfOW+AjcBDYB/dMhop/hSESJWhG3kNOkD4VxiZhCevENGBWHkDLSj3JrEV30F8oEhloKmUHmsN5SyNYMCjlpKaQ8pk5KqnLoNzYvLIU8i0HEWyAfaAHcjbD7bClE0ghLHHMzYGBehO1HSyFfUuj0B5keMteBtcDAAP+tgKqQ96sthXj7ijBI2rIbmAsM18y4k3QwCY4dIb7vWwq5HCFiJ5BtwJEX4r/URkU9ya4Qkn2GHLkhHFsShiTLQkhyDTnahXAsTBiSjAwgeGxG8IunNoBnsCVBS6DOR7DHjCA8FquB5tYkpT6S6aYE9RyFPo791gTNgGKH4BHQ2knvHwCrgG5J+Oym7zzw8imgM/Dc4dllLWSBOpZUvgIY4bTJh86D7OWLgAlAVoCfLG0r8u37fyaGQB9ZqSQ1MR954Jo6nZtE58oCbE8lIbpA7c5aCvmso9EiRSFlSQjJVruaOJbFjgGxU24wtcRHM59tL22rshTiTYmZAW0XJR9KIdjviY+IuCyx0OA5na9OJdDbmTkO58uWD65y5ls6bg5cVcdy0t7TzHlDrr7OdC0PmqKpEuQ4v1Jt2NWATpnlwBHdWb7XP3k+rHlb15B3BzkZREVsp/XSAeCSEs0IGLXCgFQmCLLjXONPP4CF2n5e9jOxiHDINijZSt9qdNDpaNQe32074E4dYJ3Wr45VhJItUrLNTt0SrXsFjNVT9TBsBMapraDA8bPXPMAjhIxWsitO3S2tm6rlExFCjqvNNC3fdPxUaF389yVAG/3Sy5lUB62TYBa01PKdCCG3nQMHQZ1zB+nFT4MMIi4xXqqR546I0y6nJmGodOwEN/R5jpaL0yJCSRf7psmPBM9p90YoCO99Qn7ECHBOy7PSKUSm10slHq+rVpGvg6Fw7Pbru97dy9NkjpCsDyQeAt3db0ISQuTb01MFCBalVYR2IsuJlevuJWYSQgY4K16JPwNOpxg5TbygHalthJA3WiXx0T4jIpzOtNUDvG+NECJL+HaJuURTATDMefZD/g+lwXmY6bVaHAgQMknFSPY8MfG3gJAR+OtABkbgO/RL/KK/Q1kvAAAAAElFTkSuQmCC"><br>{$loc["h_sickness"]}</h3>
                <a href="/suite/worktime/sick"><button class="button">{$loc["s_link"]}</button></a>
            </div>

DATA;
            return $data;
        }

        private static function get_easymode_html(){
            $nodes = new Nodes;
            if($nodes->checkNode("mode.inc", "get_easymode_html") == false){
                return;
            }
            $i18n = new i18n;
            $loc = $i18n->loadLanguage(null, "mode/easymode");
            $active = Arbeitszeit::check_easymode_worktime_finished($_SESSION["username"]);
            $worktime = Arbeitszeit::get_worktime_by_id($active);


            if($active === false){
                Exceptions::error_rep("No or multiple active easymode worktime entry found. Checking for multiple entries...");
                if(Arbeitszeit::fix_easymode_worktime($_SESSION["username"])){
                    self::get_easymode_html();
                }
                $data = <<< DATA
                <p>An error occured while checking for active easymode entries. Either a connection error to the database or you have multiple entries marked as active. If the problem persists, contact the system administrator!</p>
                <p style="font-family:monospace;">Error-Code: DEM-CHK_FAIL_EM_ENY_AC</p>
DATA;
                goto skip_to_output;
            } elseif(!$worktime && $active === true){
                Exceptions::error_rep("An error occured while checking for active easymode entries. Please ask your administrator to remove the current active worktime entry! | Active worktime: " . $worktime);
                $data = <<< DATA
                <p>An error occured while checking for active easymode entries. Please ask your administrator to remove the current active worktime entry!</p>
DATA;
            
            } elseif($active == -1){
                $data = <<< DATA
                <div class="box">
                <!--<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAQ0lEQVR4nDXMMQqAMBREwUDAKufwEHoFQ7SyUrCw8v71SOCnessUm5Cx44jmFKNgibaBE16c2AbOuPDg69g/GypurD+SzVDTKu3cOgAAAABJRU5ErkJggg==">
 -->               <p>{$loc["easymode_enabled_note"]}</p>
                    <form action="/suite/actions/worktime/easymode.php" method="POST">
                        <input type="text" name="username" value="{$_SESSION["username"]}" hidden>
                        <input type="text" name="type" value="start" hidden>
                        <button type="submit" class="button">{$loc["start_shift"]}</button>
                    </form>
                </div>

                <div class="box">
                <h3> <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAADqElEQVR4nO2ZW4hNYRTH95gxjGsSRcileJgHSpTbywzzIuVWLoUxyaVR8yKjyC0PpJRLqHlQ8iQvkhc8uCslhNxzCYNSIsO4/bTGf/PZZp9z9rnMnM351+7svc931rf+37p831rH8wr4hwGMAm7oGuXFDUARsBRo4jc+AfVABy8OAPoAxwjHCaCfl88AqoDGgOL3dbmwMVVevgEoBbYD36XoWeCd7qt1GT4A53X/Xb8p9fIBwBDgkpT7AmwEavT8ACgBioG7ercEqAM+6/kKMLy9SSwE3kuhx8CEgNLVzthqZ5xZcKyIoqRQ1x4EegCHHJ8/AvQKKNxiDec3LsGaZHLagsTYsJUMs0aYVUIs+wSYlEsCHZL5dpg1QoguDnw3QjINXxVrxdkmMRA442SbnUCnBEr+ZY1UyAIdReCbxlwChmaLxAzgjQS/BqZGVTAqYWAy8EJj3gLzMiFQppX3cQron65yUUkDfYHjzvwHga7pEPH3BhQXR4F9Mn0tMBOYaHGicxXawUtSkF3i7PbLJGOiZNZqDpvrqBOThovpELHdOd9wJjIRx736A6OBaUqT9XK5wzpu3JIfp4uPigeTc1IutFUZcqHipVx6FHntBV9bL+6gQCTPQMEifwLoAmzW3tOsT0vTZV5cLMLP7HkxJOtdaBMyWSKySWIeAZW2wyst2ynasCF7GueWyAOJqQi8NzKG+xkr2kZEmiWmW+B9d71vyljRbBLR4XM+cFoF1gXt7r5FKgPjp+j9zZwRcCZbYU2JFIq2ecDtkIBu1udjuVM3kfBjxHDH6plsKGwBuE09Kzt7HbBmnb6rSEBgjs5ZPh6q0zLMqkiHhNVCiXAllZN3MhJjnHrEha3YyBACs9UTxslIS4KrCuzW9+uA9SqDXdgizM2oFSufXuvUDNfUnBgu/0ZNhelOT9hqjuuOIk+B5WGNOmCLxp0LuJJhdca9ZGCwhPt1/A63jrd7HclRDb4LuOoo8QxYGaz9A3OUAy8DylssXNb9mqhKl8r//To6EzSq1uicZJEaAtWhue4CeYElhuiFlgqdTPEKWGVnpgTzDAL2OwFuuKcU/Ks1BPRWrBjRnlGIPG8RCeMirUC0dtPeZARcOGevWV6qcP4qGJ/yj1KTOwDYoz+BfFhTYlGyVMrPLGZoSDaJ/SETN5xrjUgs4YUR8WIC/hsicYPXChF/144TzkYyW/C5rUCmehSI5KtFgsi2ojnXg9YDP3Fg5QDkiR4FeGngB1llV2/2JzkbAAAAAElFTkSuQmCC"><br>{$loc["h_vacation"]}</h3>
                <p>{$loc["v_note"]}</p>
                <a href="/suite/worktime/vacation"><button class="button">{$loc["v_link"]}</button></a>
            </div>

            <div class="box">
                <h3><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAADw0lEQVR4nO2aW4hOURTHPyOXkjDuucRIqSkUwgvJJVIUU8rkEsLL5JKUB5oHiSfekBAlySiNGIwMQlPuuV+m3EeDmRqMu5+WWYfdmXMO33zrfN+Qf02dvfc667//395rn7X3nkTiP/5RAD2A2cA24AxQCdRQj0/AE+AosBTISTQ1AJO1g1/5c4jtXqB3UxCQA5wiNbwGxmRSxESgFhu8A4ZkQsQknfOW+AjcBDYB/dMhop/hSESJWhG3kNOkD4VxiZhCevENGBWHkDLSj3JrEV30F8oEhloKmUHmsN5SyNYMCjlpKaQ8pk5KqnLoNzYvLIU8i0HEWyAfaAHcjbD7bClE0ghLHHMzYGBehO1HSyFfUuj0B5keMteBtcDAAP+tgKqQ96sthXj7ijBI2rIbmAsM18y4k3QwCY4dIb7vWwq5HCFiJ5BtwJEX4r/URkU9ya4Qkn2GHLkhHFsShiTLQkhyDTnahXAsTBiSjAwgeGxG8IunNoBnsCVBS6DOR7DHjCA8FquB5tYkpT6S6aYE9RyFPo791gTNgGKH4BHQ2knvHwCrgG5J+Oym7zzw8imgM/Dc4dllLWSBOpZUvgIY4bTJh86D7OWLgAlAVoCfLG0r8u37fyaGQB9ZqSQ1MR954Jo6nZtE58oCbE8lIbpA7c5aCvmso9EiRSFlSQjJVruaOJbFjgGxU24wtcRHM59tL22rshTiTYmZAW0XJR9KIdjviY+IuCyx0OA5na9OJdDbmTkO58uWD65y5ls6bg5cVcdy0t7TzHlDrr7OdC0PmqKpEuQ4v1Jt2NWATpnlwBHdWb7XP3k+rHlb15B3BzkZREVsp/XSAeCSEs0IGLXCgFQmCLLjXONPP4CF2n5e9jOxiHDINijZSt9qdNDpaNQe32074E4dYJ3Wr45VhJItUrLNTt0SrXsFjNVT9TBsBMapraDA8bPXPMAjhIxWsitO3S2tm6rlExFCjqvNNC3fdPxUaF389yVAG/3Sy5lUB62TYBa01PKdCCG3nQMHQZ1zB+nFT4MMIi4xXqqR546I0y6nJmGodOwEN/R5jpaL0yJCSRf7psmPBM9p90YoCO99Qn7ECHBOy7PSKUSm10slHq+rVpGvg6Fw7Pbru97dy9NkjpCsDyQeAt3db0ISQuTb01MFCBalVYR2IsuJlevuJWYSQgY4K16JPwNOpxg5TbygHalthJA3WiXx0T4jIpzOtNUDvG+NECJL+HaJuURTATDMefZD/g+lwXmY6bVaHAgQMknFSPY8MfG3gJAR+OtABkbgO/RL/KK/Q1kvAAAAAElFTkSuQmCC"><br>{$loc["h_sickness"]}</h3>
                <a href="/suite/worktime/sick"><button class="button">{$loc["s_link"]}</button></a>
            </div>

DATA;
            } else {
                $worktimedata = Arbeitszeit::get_worktime_by_id($active);
                if($worktimedata["pause_start"] == null){
                    $data = <<< DATA
                    <div class="box">
                    <p>{$loc["note_ps"]}</p>
                    <small style="font-family:monospace;">ID: {$active}</small>
                        <form action="/suite/actions/worktime/easymode.php" method="POST">
                            <input type="text" name="username" value="{$_SESSION["username"]}" hidden>
                            <input type="text" name="type" value="pause_start" hidden>
                            <input type="text" name="id" value="{$active}" hidden>
                            <button type="submit" class="button">{$loc["start_pause"]}</button>
                        </form>
                    </div>
    DATA;
                } elseif($worktimedata["pause_end"] == null){
                    $data = <<< DATA
                    <div class="box">
                    <p>{$loc["note_pe"]}</p>
                    <small style="font-family:monospace;">ID: {$active}</small>
                        <form action="/suite/actions/worktime/easymode.php" method="POST">
                            <input type="text" name="username" value="{$_SESSION["username"]}" hidden>
                            <input type="text" name="type" value="pause_end" hidden>
                            <input type="text" name="id" value="{$active}" hidden>
                            <button type="submit" class="button">{$loc["end_pause"]}</button>
                        </form>
                    </div>
    DATA;
                } else {
                    $data = <<< DATA
                <div class="box">
                <p>{$loc["note_se"]}</p>
                <small style="font-family:monospace;">ID: {$active}</small>
                    <form action="/suite/actions/worktime/easymode.php" method="POST">
                        <input type="text" name="username" value="{$_SESSION["username"]}" hidden>
                        <input type="text" name="type" value="stop" hidden>
                        <input type="text" name="id" value="{$active}" hidden>
                        <button type="submit" class="button">{$loc["end_shift"]}</button>
                    </form>
                </div>
DATA;
                }
            }
            skip_to_output:
            return $data;
        }
    }
}



?>