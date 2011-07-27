<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="../asset/styles.css" rel="stylesheet" type="text/css"/>
        <title></title>
    </head>
    <body>
        <header>
            <h1>Drag and Drop demo<a href="?reset=true" class="button">reset</a></h1>
            <p>Click on a box to edit it, drag a box to move it.</p>
            <p>Data is saved in the session, you can refresh the page at any time.</p>
        </header>
        <section>
            <div id="add_poll" class="poll">Create a Poll</div>
            <?php foreach($polls as $poll): ?>
                <div id="poll_<?php echo $poll['id'] ?>" class="poll"><?php echo $poll['name'] ?></div>
            <?php endforeach ?>
        </section>
        <form>
            <div class="poll-select"><div>&nbsp;</div></div>
            <fieldset>
                <label for="form_name">Name:</label>
                <input type="text" id="form_name" name="name" />

                <input type="checkbox" id="form_visible" name="visible" />
                <label for="form_visible">Is visible</label>

                <div class="buttons">
                    <a id="save" class="button">Save</a>
                    <a id="delete" class="button">delete</a>
                </div>
            </fieldset>
            <fieldset class="poll_choices">
                <?php foreach (range(1, 5) as $index => $nb): ?>
                    <label for="form_choices_<?php echo $index ?>">Choice <?php echo $nb ?>:</label>
                    <input type="text" id="form_choices_<?php echo $index ?>" name="choices[<?php echo $index ?>]" />
                <?php endforeach ?>
            </fieldset>
        </form>
        <script type="text/javascript" src="../asset/jquery-1.5.1.min.js"></script>
        <script type="text/javascript" src="../asset/jquery-ui-1.8.14.custom.min.js"></script>
        <script type="text/javascript" src="../asset/dad-widget.js"></script>
        <script>
            $(function() {
                $('section').dad({
                    polls: <?php echo json_encode($polls) ?>,
                    create_url: 'ajax/create',
                    update_url: 'ajax/edit',
                    delete_url: 'ajax/delete',
                    rowSize: 3,
                    width: 600, // in pixels
                    new: {
                        name: 'new poll',
                        choices: ['uno', 'dos', 'tres']
                    },
                    uiSortable: {
                        containment: 'parent',
                        placeholder: 'ghost',
                        items: '> div:not(#add_poll)',
                        cursor: 'pointer',
                    },
                });
            });
        </script>
    </body>
</html>
