<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Leaderboard settings</h4>
                <?php
                if (isset($_SESSION['message'])) {
                    echo $_SESSION['message'];
                }
                ?>
                <form action="<?= site_url('admin/leaderboard/update') ?>" method="POST">
                    <div class="alert alert-info text-center">
                        You can add maximum 15 prizes for each contest, each prize seperated by | for example: 0.2|0.1|0.05|0.001
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Activity contest reward</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-4" name="activity_contest_reward" value="<?= $leaderboardSettings['activity_contest_reward'] ?>">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Activity contest requirement</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-4" name="activity_contest_requirement" value="<?= $leaderboardSettings['activity_contest_requirement'] ?>">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Referral contest reward</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-4" name="referral_contest_reward" value="<?= $leaderboardSettings['referral_contest_reward'] ?>">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Referral contest requirements</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-4" name="referral_contest_requirement" value="<?= $leaderboardSettings['referral_contest_requirement'] ?>">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Faucet contest reward</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-4" name="faucet_contest_reward" value="<?= $leaderboardSettings['faucet_contest_reward'] ?>">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Faucet contest requirement</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-4" name="faucet_contest_requirement" value="<?= $leaderboardSettings['faucet_contest_requirement'] ?>">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Shortlink contest reward</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-4" name="shortlink_contest_reward" value="<?= $leaderboardSettings['shortlink_contest_reward'] ?>">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Shortlink contest requirement</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-4" name="shortlink_contest_requirement" value="<?= $leaderboardSettings['shortlink_contest_requirement'] ?>">
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Offerwall contest minimum value</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-4" name="offerwall_contest_min_value" value="<?= $leaderboardSettings['offerwall_contest_min_value'] ?>">
                        </div>
                    </div>
                    <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>">

                    <div class="form-group row justify-content-end">
                        <div class="col-sm-9">
                            <div>
                                <button type="submit" class="btn btn-primary w-md">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>