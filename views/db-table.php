<html>
    <head>
           <title>Database Diagram</title>
        <style>
            table{
                width: 100%;
                background: #e6e6e6;
                font-family: Arial;
            }
            td {
                background: #fff;
                width: 50%;
                padding: 3px 5px;
                font-size: 12px;
            }
            .head td {
                background: #8d8d8d !important;
                color: #fff;
            }
        </style>
    </head>
    <body>
        <?php foreach ($tables as $name => $columns) : ?>
            <p>Table Name : <b><?= $name ?></b></p>
            <table>
                <tr class="head"><td>Column Name</td><td>Column Type</td></tr>
                <?php foreach ($columns as $column) { ?>
                    <tr><td><?= $column[0] ?></td><td><?= $column[1] ?></td></tr>
                <?php }?>
            </table>
            <br>
        <?php  endforeach; ?>

    </body>
</html>