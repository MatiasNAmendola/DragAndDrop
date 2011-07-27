var self;

$.widget('balloon.dad',
{
    _init: function()
    {
        self = this;

        $element = $(this.element);
        $expand = $('form');
        $select = $('.poll-select');

        this.bind(self.options.polls);

        $element.sortable($.extend(this.options.uiSortable, {
            start: this.startMoving,
            update: this.endMoving
        }));

        $element.delegate('.poll', 'click', this.expand);
        $expand.find('#save').click(this.save);
        $expand.find('#delete').click(this.delete);
    },


    /**
     * --------------------------------
     * bind from data
     * --------------------------------
     */
    bind: function(data)
    {
        // new element
        $element.children('#add_poll').data('poll', self.options.new);

        // bind data to DOM elements
        for(i in data) {
            $element.children('#poll_'+data[i].id).data('poll', data[i]);
        }
    },

    /**
     * --------------------------------
     * start drag and drop
     * --------------------------------
     */
    startMoving: function(event, ui)
    {
        // remove expand to not break layout
        $expand.hide().insertAfter($element);

        // save data about current moving item
        self.domMovingIndex = ui.item.index();
    },


    /**
     * --------------------------------
     * end drag and drop
     * --------------------------------
     */
    endMoving: function(event, ui)
    {
        // get position of dropped item
        // minus one because there's the create block
        var data = { 'order': { 'from': self.domMovingIndex - 1, 'to': ui.item.index() - 1 } };

        // send ajax request to update order
        $.post(self.options.update_url+'/'+ui.item.data('poll').id, data, function(poll) {
            $element.children('#poll_'+poll.id).data('poll', poll);
        });
    },


    /**
     * --------------------------------
     * expand form
     * --------------------------------
     */
    expand: function()
    {
        // bind expand form with data
        var poll = $(this).data('poll');
        $expand.find('#form_name').val(poll.name);
        $expand.find('#form_visible').attr('checked', poll.visible);
        for(i in [0, 1, 2, 3, 4]) {
            $expand.find('#form_choices_'+i).val(poll.choices[i]);
        }

        // index of clicked element
        var elementPos = $(this).index('section > div') + 1;

        // calculate if $expand is visible or not
        var isVisible = !$expand.is(':visible') || (undefined !== self.currentPoll && poll !== self.currentPoll);
        self.currentPoll = poll;

        // we insert expand in the DOM
        var expandPos = Math.ceil(elementPos / self.options.rowSize) * self.options.rowSize - 1;
        $switchElement = $element.children('div:eq('+expandPos+')');
        $expand[isVisible ? 'slideDown' : 'slideUp']().insertAfter(
            $switchElement.length == 0 ? $element.children('div:last') : $switchElement
        );

        // element index inside current row
        var rangIndex = (elementPos > self.options.rowSize)
            ? elementPos - ((Math.ceil(elementPos / self.options.rowSize) - 1) * self.options.rowSize)
            : elementPos;

        // move cursor
        var partWidth = $element.width() / self.options.rowSize;
        var cursorLeft =  partWidth * rangIndex - (partWidth / 2) - 37;
        $select.filter('div').animate({'margin-left': cursorLeft}, 300);
    },


    /**
     * --------------------------------
     * add/edit a Poll
     * --------------------------------
     */
    save: function()
    {
        // get form data
        var choices = [];
        for(i in [0, 1, 2, 3, 4]) {
            choices.push($expand.find('#form_choices_'+i).val());
        }

        var url = (isNew = undefined === self.currentPoll.id)
            ? self.options.create_url
            : self.options.update_url+'/'+self.currentPoll.id;

        var data = {
            'name': $expand.find('#form_name').val(),
            'choices': choices,
            'visible': $expand.find('#form_visible').is(':checked') ? 1 : 0,
        };

        // send ajax request
        $.post(url, data, function(poll) {
            $expand.hide();

            // create the new element
            isNew && $element.append($('<div/>').attr('id', 'poll_'+poll.id).attr('class', 'poll'));

            // update view data
            $element.children('#poll_'+poll.id)
                .data('poll', poll)
                .text(poll.name);
        }, 'json');

        return false;
    },


    /**
     * --------------------------------
     * delete a Poll
     * --------------------------------
     */
    delete: function()
    {
        $.post(self.options.delete_url+'/'+self.currentPoll.id, function(poll) {
            $expand.hide();
            $('#poll_'+poll.id).fadeOut().remove();
        }, 'json');
        return false;
    },
});
