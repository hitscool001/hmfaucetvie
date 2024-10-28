<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title mb-4">Users</h4>
                <?php
                if (isset($_SESSION['message'])) {
                    echo $_SESSION['message'];
                }
                ?>
                <div class="table-responsive">
                    <form action="#" id="submit-form" method="POST">
                        <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>">
                        <input type="hidden" name="from" value="<?= current_url() . '/?' . $_SERVER['QUERY_STRING'] ?>">
                        <button type="submit" class="btn btn-danger suspect-button" submitto="<?= site_url('/admin/users/mass_ban?not_allowed_email=true') ?>">Ban selected</button>
                        <button type="submit" class="btn btn-success suspect-button" submitto="<?= site_url('/admin/users/mass_release?not_allowed_email=true') ?>">Release selected</button>
                        <table class="table table-striped" style="font-size: 10px;">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Last Active</th>
                                    <th scope="col">Balance</th>
                                    <th scope="col">Ip Address</th>
                                    <th scope="col">Referred By</th>
                                    <th scope="col">Verified</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 10px;">
                                <?php foreach ($users as $user) { ?>
                                    <tr>
                                        <th scope="row">
                                            <div class="form-group form-check">
                                                <input type="checkbox" class="form-check-input check-<?= $user['id'] ?>" name="userId[]" value="<?= $user['id'] ?>">
                                                <label class="form-check-label"><?= $user['id'] ?></label>
                                            </div>
                                        </th>
                                        <td><?= $user['email'] ?> <?= ($user['isocode'] != 'N/A') ? '<img src="' . base_url() . 'assets/images/flags/' . $user['isocode'] . '.png" title="' . $user['country'] . '">' : '' ?></td>
                                        <td><?= $user['username'] ?></td>
                                        <td><?= timespan($user["last_active"], time(), 2) ?> ago</td>
                                        <td><?= $user['balance'] ?></td>
                                        <td class="admin-ip-address"><?= $user['ip_address'] ?></td>
                                        <td><a href="<?= site_url('admin/users/details/' . $user['referred_by']) ?>"><?= $user['referred_by'] ?></a></td>
                                        <td><?= $user['verified'] ?></td>
                                        <td><a href="<?= site_url("/admin/users/details/" . $user['id']) ?>" class="btn btn-secondary btn-sm" title="Detail"><i class="fas fa-user-cog"></i></a></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </form>
                </div>
                <?= $pagination ?>
            </div>
        </div>
    </div>
</div>