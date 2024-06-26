<?php $__env->startSection('content'); ?>
<h1>Quản lý người dùng</h1>
<?php if(can('users.add')): ?>
<a href="<?php echo e(url('users.add')); ?>" class="btn btn-primary my-2">Thêm mới</a>
<?php endif; ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th width="5%">STT</th>
            <th>Tên</th>
            <th>Email</th>
            <th width="20%">Trạng thái</th>
            <?php if(can('users.update')): ?>
            <th width="5%">Sửa</th>
            <?php endif; ?>
            <?php if(can('users.delete')): ?>
            <th width="5%">Xóa</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($key + 1); ?></td>
            <td><?php echo e($user->name); ?></td>
            <td><?php echo e($user->email); ?></td>
            <td><?php echo $user->status ? '<span class="badge bg-success">Kích hoạt</span>':'<span class="badge bg-danger">Chưa kích hoạt</span>'; ?></td>
            <?php if(can('users.update')): ?>
            <td><a href="<?php echo e(url('users.edit', ['id' => $user->id])); ?>" class="btn btn-warning btn-sm">Sửa</a></td>
            <?php endif; ?>
            <?php if(can('users.delete')): ?>
            <td>
                <form method="post" onsubmit="return confirm('Bạn có chắc chắn?')" action="<?php echo e(url('users.delete', ['id' => $user->id])); ?>">
                    <button class="btn btn-danger btn-sm">Xóa</button>
                </form>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>