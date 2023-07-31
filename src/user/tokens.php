<?php
require_once "../app/require.php";
$user = new UserController();


Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

$username = Session::get('username');
$uid = Session::get("uid");
$tokenarray = $user->gettokenarray();

Util::banCheck();
Util::checktoken();
Util::head("Tokens");
Util::navbar();




if (Util::securevar($_SERVER['REQUEST_METHOD']) === 'POST') {
    if (isset($_POST["password2"])) {
        $token = Util::securevar($_POST["deltoken"]);
        $password = Util::securevar($_POST["password2"]);
        if (isset($token, $password)) {
            $user->deletetoken($token, $password);
            header("location: tokens.php");
            exit();
        }
    }
    if (isset($_POST["password"])) {
        $password = Util::securevar($_POST["password"]);

        $token = Util::securevar($_COOKIE['login_cookie']);
        $error = $user->deleteother($token, $password);
        header('location: tokens.php');
        exit();
    }


    if (isset($_POST["setnote"])) {
        $selectedTokenId = Util::securevar($_POST["setnote"]);
        $note = Util::securevar($_POST["note"]);
        $error = $user->setTokenNoteById($selectedTokenId, $note);
        header('location: tokens.php');
        exit();
    }
}




?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<style>
    .divide {
        padding: 0;
        margin: 0;
        margin-bottom: 30px;
        background: #1e5799;
        background: -moz-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
        background: -webkit-gradient(linear, left top, right top, color-stop(0%, #1e5799), color-stop(50%, #f300ff), color-stop(100%, #e0ff00));
        background: -webkit-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
        background: -o-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
        background: -ms-linear-gradient(left, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
        background: linear-gradient(to right, #1e5799 0%, #f300ff 50%, #e0ff00 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#1e5799', endColorstr='#e0ff00', GradientType=1);
        height: 3px;
        border-bottom: 1px solid #000;
    }

    .modal-content {
        background-color: #101010 !important;
    }

    .modal-title {
        color: white !important;
    }

    .modal-body {
        color: white !important;
    }

    .modal-footer {
        border-top: 1px solid #444444 !important;
    }

    table.rounded {
        margin-top: 20px !important;
    }
</style>


<div class="divide"></div>
<main class="container mt-2">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="<?php Util::Display(Util::securevar($_SERVER["PHP_SELF"])); ?>" method="post">
                        <label for="id">Select a token by ID:</label><br>
                        <select name="setnote" class="form-control form-control-sm">
                            <br>
                            <?php foreach ($tokenarray as $row) : ?>
                                <?php Util::Display("<option value='$row->id'>" . "$row->id </option>"); ?>
                            <?php endforeach; ?>
                        </select>
                        <br>
                        <label>Enter a note to the selected token</label><br>
                        <input autocomplete="off" type="text" class="form-control form-control-sm" placeholder="iPhone" name="note" required>
                        <br>
                        <button class="btn btn-outline-primary btn-block" id="submit" type="submit" value="submit">Set note</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-outline-primary btn-block" onclick="openPasswordModal()">Log out of all other devices</a>
                </div>
            </div>
        </div>
        <br>
        <br>
        <br>
        <table class="rounded table">
            <thead>
                <tr>
                    <th scope="col">IP</th>
                    <th scope="col">ID</th>
                    <th scope="col">Token</th>
                    <th scope="col">Note</th>
                    <th scope="col">Last used</th>
                    <th scope="col">Browser</th>
                    <th scope="col">OS</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tokenarray as $row) : ?>
                    <tr style="text-align: center;">
                        <td>
                            <p onclick="copyToClipboard('<?php Util::display($row->ip); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top' class='spoiler'><?php Util::display($row->ip); ?></p>
                        </td>
                        <td>
                            <p><?php Util::display($row->id); ?></p>
                        </td>
                        <td>
                            <p onclick="copyToClipboard('<?php Util::display($row->remembertoken); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top' class='spoiler'><?php Util::display($row->remembertoken); ?></p>
                        </td>
                        <td>
                            <p><?php Util::display($row->note); ?></p>
                        </td>
                        <td>
                            <p><?php Util::display($row->time); ?></p>
                        </td>
                        <td>
                            <p><?php Util::display($row->browser); ?></p>
                        </td>
                        <td>
                            <p><?php Util::display($row->os); ?></p>
                        </td>


                        <td><a class="btn btn-outline-primary btn-sm delete-token" onclick="openPasswordModal2('<?php Util::Display(Util::securevar($row->remembertoken)); ?>')">Delete</a>
                            <br>
                            <?php if ($row->remembertoken ==  Util::securevar($_COOKIE["login_cookie"])) : ?>
                                <img title="You are currently using this token to login" data-toggle="tooltip" data-placement="top" src="../assets/img/warning.png" width="15" height="15">
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="passwordModalLabel">Enter Password to logout of all other devices</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="passwordform">
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" form="passwordForm" class="btn btn-outline-primary btn-block" onclick="submitForm()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="passwordModal2" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel2" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="passwordModalLabel2">Enter Password to delete this token</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="passwordform2">
                            <div class="form-group">
                                <label for="password2">Password:</label>
                                <input type="password" class="form-control" id="password2" name="password2" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" form="passwordform2" class="btn btn-outline-primary btn-block" onclick="submitForm2()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Function to open the Bootstrap modal dialog
            function openPasswordModal() {
                $('#passwordModal').modal('show');
            }

            // Function to handle form submission
            function submitForm() {
                $('#passwordform').submit(); // Submit the form
            }


            // Function to open the Bootstrap modal dialog
            function openPasswordModal2(token) {
                $("#passwordModal2").data("token", token);
                $('#passwordModal2').modal('show');
            }

            // Function to handle form submission
            function submitForm2() {
                const token = $("#passwordModal2").data("token");
                if (token) {
                    // Set the token as a hidden input in the form
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'deltoken',
                        value: token
                    }).appendTo('#passwordform2');
                }
                $('#passwordform2').submit(); // Submit the form
            }
        </script>

    </div>
    <br>
</main>
<style>
    .spoiler:hover {
        color: white;
    }

    .spoiler {
        color: black;
        background-color: black;
    }

    p {
        max-width: fit-content;
    }

    /* ===== Scrollbar CSS ===== */
    /* Firefox */
    * {
        scrollbar-width: auto;
        scrollbar-color: #6cc312 #222222;
    }

    /* Chrome, Edge, and Safari */
    *::-webkit-scrollbar {
        width: 16px;
    }

    *::-webkit-scrollbar-track {
        background: #222222;
    }

    *::-webkit-scrollbar-thumb {
        background-color: #6cc312;
        border-radius: 10px;
        border: 3px solid #222222;
    }
</style>
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    function copyToClipboard(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
    }
</script>
<?php Util::footer(); ?>