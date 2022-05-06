<?php
$load = new Load();

$parentDir = Load::getParentDir();

$dataScan = $load->scanDir($parentDir);
?>
<table id="dataTable">
    <thead>
        <tr>
            <th class="text-center"><input type="checkbox" id="checkAll" /> </th>
            <th>Tên</th>
            <th>Dung lượng</th>
            <th>Cập nhật cuối</th>
            <th>Quyền</th>
            <th class="text-end">Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php
            if (!empty($dataScan)):
                foreach ($dataScan as $item):
                    if ($item!=='.DS_Store'):
                    $path = $load->getPath($item);

                    if ($load->isType($path)=='folder'){
                        $targetPath = str_replace(_DATA_DIR.'/', '', $path);
                    }else{
                        $targetPath = '';
                    }

        ?>
        <tr>
            <td class="text-center"><input type="checkbox" class="check-item" /></td>
            <td><a href="?path=<?php echo urlencode($targetPath); ?>"><?php echo $load->getTypeIcon($item).' '.$item; ?></a></td>
            <td><?php echo $load->getSize($item, 'KB'); ?></td>
            <td><?php echo $load->getTimeModify($item); ?></td>
            <td><?php echo $load->getPermission($item); ?></td>
            <td class="text-end">
                <?php if ($load->isType($path)=='file'): ?>
                    <a href="#" class="btn btn-primary btn-sm mx-1"><i class="fa fa-eye" aria-hidden="true"></i></a>
                    <?php endif; ?>
                <a href="#" class="btn btn-primary btn-sm mx-1"><i class="fa fa-trash" aria-hidden="true"></i>
                <a href="#" class="btn btn-primary btn-sm mx-1"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                <a href="#" class="btn btn-primary btn-sm mx-1"><i class="fa fa-files-o" aria-hidden="true"></i>
                <a href="#" class="btn btn-primary btn-sm mx-1"><i class="fa fa-link" aria-hidden="true"></i>
                </a>
                <?php if ($load->isType($path)=='file'): ?>
                    <a href="#" class="btn btn-primary btn-sm mx-1"><i class="fa fa-download" aria-hidden="true"></i></a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; endforeach; endif; ?>
    </tbody>
</table>