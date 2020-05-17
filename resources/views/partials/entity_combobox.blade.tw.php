var {{ $entityType }}Name = '';

${{ $entityType }}Select.combobox({
    highlighter: function (item) {
        if (item.indexOf("{{ trans("texts.create_{$entityType}") }}") == 0) {
            {{ $entityType }}Name = this.query;
            return "{{ trans("texts.create_{$entityType}") }}: " + this.query;
        } else {
            var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&');
            item = _.escape(item);
            return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
              return match ? '<strong>' + match + '</strong>' : query;
            })
        }
    },
    template: '<div class="combobox-container"> <input type="hidden" /> <div class="relative flex items-stretch w-full"> <input type="text" id="{{ $entityType }}_name" name="{{ $entityType }}_name" autocomplete="off" /> <span class="py-1 px-2 mb-1 text-base font-normal leading-normal text-grey-darkest text-center bg-grey-light border border-4 border-grey-lighter rounded  inline-block w-0 h-0 ml-1 align border-b-0 border-t-1 border-r-1 border-l-1" data-dropdown="relative"> <span class="caret" /> <i class="fa fa-times"></i> </span> </div> </div> ',
    matcher: function (item) {
        // if the user has entered a value show the 'Create ...' option
        if (item.indexOf("{{ trans("texts.create_{$entityType}") }}") == 0) {
            return this.query.length;
        }
        return ~item.toLowerCase().indexOf(this.query.toLowerCase());
    }
}).on('change', function(e) {
    var {{ $entityType }}Id = $('input[name={{ $entityType }}_id]').val();
    if ({{ $entityType }}Id == '-1') {
        $('#{{ $entityType }}_name').val({{ $entityType }}Name);
    }
});
