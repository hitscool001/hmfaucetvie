<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4 text-center">Coupon settings</h4>
                <form action="<?= site_url('admin/coupon/add') ?>" method="post" autocomplete="off">
                    <?php
                    if (isset($_SESSION['message'])) {
                        echo $_SESSION['message'];
                    }
                    ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-1">
                                <label class="form-label">Code</label>
                                <input type="text" class="form-control" name="code" placeholder="Code" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-1">
                                <label class="form-label" for="basicInput">Balance reward</label>
                                <input type="number" min="0" step="0.00001" class="form-control" name="balance_reward" placeholder="Balance reward" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-1">
                                <label class="form-label" for="basicInput">Deposit balance reward</label>
                                <input type="number" min="0" step="0.00001" class="form-control" name="dep_balance_reward" placeholder="Deposit balance reward" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-1">
                                <label class="form-label" for="basicInput">Energy reward</label>
                                <input type="number" class="form-control" name="energy_reward" placeholder="Energy reward" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-1">
                                <label class="form-label">Number of use</label>
                                <input type="text" type="number" min="0" class="form-control" name="number_of_use" placeholder="Number of use" required>
                                <small>0 means no limit</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Advertising discount</label>
                            <div class="input-group mb-1">
                                <input type="text" type="number" min="0" class="form-control" name="advertising_discount" placeholder="Advertising discount" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Expired in</label>
                            <div class="input-group mb-1">
                                <input type="text" type="number" min="0" class="form-control" name="expired_in" placeholder="Expired in" required>
                                <span class="input-group-text">days</span>
                            </div>
                            <small>0 means never expire</small>
                        </div>
                    </div>
                    <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>" />
                    <button type="submit" class="btn btn-success btn-lg btn-block"><i class="fas fa-check"></i> Add</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Your coupons</h4>
                <div class="table-responsive">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Code</th>
                                <th scope="col">Balance reward</th>
                                <th scope="col">Deposit balance reward</th>
                                <th scope="col">Energy</th>
                                <th scope="col">Number of use</th>
                                <th scope="col">Used</th>
                                <th scope="col">Advertising discount</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coupons as $coupon) { ?>
                                <form action="<?= site_url('/admin/coupon/update/' . $coupon['id']) ?>" method="POST">
                                    <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>" />
                                    <tr>
                                        <th scope="row"><?= $coupon['id'] ?></th>

                                        <td>
                                            <div class="mb-1">
                                                <input type="text" class="form-control" name="code" placeholder="Code" value="<?= $coupon['code'] ?>" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                <input type="number" min="0" step="0.00001" class="form-control" name="balance_reward" placeholder="Balance reward" value="<?= $coupon['balance_reward'] ?>" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                <input type="number" min="0" step="0.00001" class="form-control" name="dep_balance_reward" placeholder="Deposit balance reward" value="<?= $coupon['dep_balance_reward'] ?>" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                <input type="number" class="form-control" name="energy_reward" placeholder="Energy reward" value="<?= $coupon['energy_reward'] ?>" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                <input type="text" type="number" min="0" class="form-control" name="number_of_use" placeholder="Number of use" value="<?= $coupon['number_of_use'] ?>" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-1">
                                                <input type="text" type="number" min="0" class="form-control" value="<?= $coupon['used'] ?>" disabled>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group mb-1">
                                                <input type="text" type="number" min="0" class="form-control" name="advertising_discount" placeholder="Advertising discount" value="<?= $coupon['advertising_discount'] ?>" required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="submit" class="edit-button btn btn-warning btn-sm">edit</button>
                                            <a class="btn btn-danger btn-sm" href="<?= site_url('admin/coupon/delete/' . $coupon['id']) ?>">delete</a>
                                        </td>
                                    </tr>
                                </form>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Coupon settings</h4>
                <?php
                if (isset($_SESSION['message'])) {
                    echo $_SESSION['message'];
                }
                ?>
                <form action="<?= site_url('admin/update/coupon') ?>" method="POST">
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Coupon status</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="coupon_status">
                                <option value="on" <?= ($settings['coupon_status'] == 'on') ? 'selected' : '' ?>>On</option>
                                <option value="off" <?= ($settings['coupon_status'] == 'off') ? 'selected' : '' ?>>Off</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Coupon top ad</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" rows="6" name="coupon_top_ad"><?= $settings['coupon_top_ad'] ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Coupon bottom ad</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" rows="6" name="coupon_bottom_ad"><?= $settings['coupon_bottom_ad'] ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Coupon footer ad</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" rows="6" name="coupon_footer_ad"><?= $settings['coupon_footer_ad'] ?></textarea>
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