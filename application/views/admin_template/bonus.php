<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Bonus settings</h4>
                <?php
                if (isset($_SESSION['message'])) {
                    echo $_SESSION['message'];
                }
                ?>
                <form action="<?= site_url('admin/bonus/add/') ?>" method="POST">
                    <input type="hidden" name="<?= $csrf_name ?>" id="token" value="<?= $csrf_hash ?>">
                    <div class="row mb-4">
                        <div class="col">
                            <label>USD Reward</label>
                            <input type="text" min="0.000001" step="0.000001" name="usd" class="form-control" value="" required="">
                        </div>
                        <div class="col">
                            <label>Energy Reward</label>
                            <input type="number" name="energy" class="form-control" value="" required="">
                        </div>
                        <div class="col">
                            <label>EXP Reward</label>
                            <input type="number" name="exp" class="form-control" value="" required="">
                        </div>
                        <div class="col">
                            <label>Lottery</label>
                            <input type="number" name="lottery" class="form-control" value="" required="">
                        </div>
                        <div class="col">
                            <label>Actions</label>
                            <div style="display: block;">
                                <button type="submit" class="btn btn-success btn-sm">Add</button>
                            </div>
                        </div>
                    </div>
                </form>
                <hr>
                <?php foreach ($bonuses as $bonus) { ?>
                    <form action="<?= site_url('admin/bonus/update/' . $bonus['id']) ?>" method="POST">
                        <input type="hidden" name="<?= $csrf_name ?>" id="token" value="<?= $csrf_hash ?>">
                        <div class="row mb-4">
                            <div class="col">
                                <label>USD Reward</label>
                                <input type="text" min="0.000001" step="0.000001" name="usd" class="form-control" value="<?= $bonus['usd'] ?>" required="">
                            </div>
                            <div class="col">
                                <label>Energy Reward</label>
                                <input type="number" name="energy" class="form-control" value="<?= $bonus['energy'] ?>" required="">
                            </div>
                            <div class="col">
                                <label>EXP Reward</label>
                                <input type="number" name="exp" class="form-control" value="<?= $bonus['exp'] ?>" required="">
                            </div>
                            <div class="col">
                                <label>Lottery</label>
                                <input type="number" name="lottery" class="form-control" value="<?= $bonus['lottery'] ?>" required="">
                            </div>
                            <div class="col">
                                <label>Actions</label>
                                <div style="display: block;">
                                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                                    <a href="<?= site_url('admin/bonus/delete/' . $bonus['id']) ?>" class="btn btn-danger btn-sm">Delete</a>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Bonus settings</h4>
                <?php
                if (isset($_SESSION['message'])) {
                    echo $_SESSION['message'];
                }
                ?>
                <form action="<?= site_url('admin/update/bonus') ?>" method="POST">
                    <div class="form-group row mb-4">
                        <label class="col-sm-3 col-form-label">Bonus status</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="bonus_status">
                                <option value="on" <?= ($settings['bonus_status'] == 'on') ? 'selected' : '' ?>>On</option>
                                <option value="off" <?= ($settings['bonus_status'] == 'off') ? 'selected' : '' ?>>Off</option>
                            </select>
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