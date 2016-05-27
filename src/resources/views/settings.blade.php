<div class="panel">
    <div class="panel-heading" data-icon="flask">
        <span class="panel-title">@lang('api::core.title.api')</span>
    </div>
    <div class="panel-body api-settings">
        <div class="form-group" id="api-keys">
            <div class="row-helper hidden padding-xs-vr">
                <div class="input-group">
                    <span class="input-group-addon api-key bg-success"></span>
                    {!! Form::text(null, null, [
                        'disabled', 'class' => 'row-value form-control',
                    ]) !!}

                    <div class="input-group-btn">
                        {!! Form::button('', [
                            'data-icon' => 'trash-o',
                            'name' => 'trash-row',
                            'class' => 'btn btn-warning remove-row'
                        ]) !!}
                    </div>
                </div>
            </div>

            <div class="rows-container"></div>

            {!! Form::button('', [
                'data-icon' => 'plus',
                'class' => 'add-row btn btn-primary',
                'data-hotkeys' => 'ctrl+a'
            ]) !!}
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    $(function () {
        var keys = Api.get('/api.keys', {}, function (response) {
            var $container = $('#api-keys').removeClass('hidden');

            $container.on('click', '.add-row', function (e) {
                e.preventDefault();

                bootbox.prompt(i18n.t('api.core.messages.new_key'), function (result) {
                    if (result === null) {
                        return true;
                    } else if (result.length > 0) {
                        Api.put('/api.key', {description: result}, function (response) {
                            if (response.content) {
                                var $row = clone_row($container);
                                fill_row($row, response.content);
                            }
                        });

                        return true;
                    }

                    return false;
                });
            });

            $container.on('click', '.remove-row', function (e) {
                var $cont = $(this).closest('.row-helper');
                Api.delete('/api.key', {key: $cont.data('id')}, function (response) {
                    if (response.content) $cont.remove();
                });
                e.preventDefault();
            });

            for (key in response.content) {
                var row = clone_row($container);
                fill_row(row, response.content[key]);
            }
        });

        function fill_row($row, $key) {
            var input = $row.find('.row-value');

            $row.find('.api-key').text($key.id);
            input.val($key.name);
            $row.data('id', $key.id);
        }

        function clone_row($container) {
            return $('.row-helper.hidden', $container)
                    .clone()
                    .removeClass('hidden')
                    .appendTo($('.rows-container', $container))
                    .find(':input')
                    .end();
        }
    });
</script>
@endpush
