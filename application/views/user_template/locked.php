<div class="card">
    <div class="card-body text-center">
        <img src="<?= site_url('assets/images/lock.png') ?>" />
        <h4>Your account is temporary locked</h4>
        <p>Unlock in:
            <span class="counter" wait="<?= $user['locked_until'] - time() ?>">
            </span>
        </p>
        <h4>How to avoid this?</h4>
        <ul>
            <li>Do not use bots</li>
            <li>Do not make too many wrong captcha/ antibotlinks</li>
        </ul>
    </div>
</div>