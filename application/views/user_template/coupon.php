<div class="card">
    <div class="card-header">
        <h4 class="card-title">Redeem coupon</h4>
    </div>
    <div class="card-body">
        <form action="<?= site_url('coupon/redeem') ?>" method="POST">
            <?php
            if (isset($_SESSION['message'])) {
                echo $_SESSION['message'];
            }
            ?>
            <input type="hidden" name="<?= $csrf_name ?>" id="token" value="<?= $csrf_hash ?>">
            <label>Coupon Code</label>
            <div class="input-group mb-4">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-file"></i>
                    </span>
                </div>
                <input type="text" class="form-control" name="code" placeholder="Enter your coupon code" aria-label="Coupon code" aria-describedby="coupon-icon" autocomplete="off">
            </div>
            <div class="mb-2">
                <?= $captchaDisplay ?>
            </div>
            <button type="submit" class="btn btn-success btn-block">Redeem</button>
        </form>
    </div>
</div>