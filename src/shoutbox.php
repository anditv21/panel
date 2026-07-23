<?php
require_once __DIR__ . "/app/require.php";

$user = new UserController();
$rateLimiter = new RateLimiter();
Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

Util::banCheck();
Util::checktoken();

$pollActor = Session::get('uid') . '|' . RateLimiter::getClientIp();
$pollLimit = $rateLimiter->hit('shoutbox.poll', $pollActor, 120, 60, 120);

if (!$pollLimit['allowed']) {
    http_response_code(429);
    exit('Too many requests.');
}

$messages = $user->getmsgs();

if (empty($messages)) :
?>
    <p class="text-center mb-0">No messages yet.</p>
<?php
endif;

foreach ($messages as $message) :
    $avatar = Util::getavatar($message['uid']);
    $usernameClass = '';

    if ($message['uid'] == Session::get('uid')) {
        $usernameClass = 'own-username';
    } elseif ($message['admin'] == 1) {
        $usernameClass = 'admin-username';
    } elseif ($message['supp'] == 1) {
        $usernameClass = 'supp-username';
    }

    $userExists = !empty($message['username']);
    $displayname = !empty($message['displayname']) ? $message['displayname'] : $message['username'];

    if (!$userExists) {
        $displayname = 'Unknown user';
    }
?>
    <div class="shoutbox-message d-flex">
        <div class="shoutbox-avatar">
            <img src="<?php Util::display($avatar ?: SUB_DIR . '/assets/images/avatars/Portrait_Placeholder.png'); ?>" class="rounded-circle img-profile" width="45" height="45">
        </div>
        <div>
            <strong class="<?php Util::display($usernameClass); ?>">
                <?php if ($userExists) : ?>
                    <a href="<?php Util::display(SUB_DIR . '/user/viewprofile.php?uid=' . $message['uid']); ?>">
                        <?php Util::display($displayname . ' (' . $message['uid'] . ')'); ?>
                    </a>
                <?php else : ?>
                    <?php Util::display($displayname); ?>
                <?php endif; ?>
            </strong>
            <small><?php Util::display($message['time']); ?></small>
            <div><?php Util::display($message['message']); ?></div>
        </div>
    </div>
<?php endforeach; ?>
