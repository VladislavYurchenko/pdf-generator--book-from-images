<?php

$current_page = basename($_SERVER['PHP_SELF']);

$nav_items = [
    'form.php' => 'Перенос файлов',
    // 'move_files.php' => 'Перенос файлов',
    // 'delete.php' => 'Удалить',
    'index.php' => 'Генератор   ',
    // 'process.php' => 'Process'
];

?>

<nav style="background-color:#f8f9fa; padding:10px; border-bottom: 1px solid #ddd;">
    <ul style="list-style-type: none; padding: 0; margin: 0;">
        <?php foreach ($nav_items as $page => $label): ?>
            <li style="display: inline; margin-right: 20px;">
                <?php
                $active_class = ($page == $current_page) ? 'active' : '';
                ?>
                <a href="<?php echo htmlspecialchars($page); ?>" style="text-decoration: none; color: #007bff; font-weight: <?php echo ($active_class ? 'bold' : 'normal'); ?>;">
                    <?php echo htmlspecialchars($label); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>