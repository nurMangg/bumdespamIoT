<div class="modal modal-blur fade" id="ajaxModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelHeading">Tambah {{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addForm" name="addForm" class="form-horizontal">
                    <div class="row">
                    <input type="hidden" name="id" id="id">
                    @foreach ($form as $field)
        <div class="form-group col-md-{{ $field['width'] ?? 12 }}">
            <label for="{{ $field['field'] }}" class="control-label">
                {{ $field['label'] }}
                @if ($field['required'] ?? false)
                    <span class="text-danger">*</span>
                @endif
            </label>
            @if ($field['type'] === 'textarea')
                <textarea class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" placeholder="{{ $field['placeholder'] ?? '' }}" {{ $field['required'] ?? false ? 'required' : '' }} {{ $field['disabled'] ?? false ? 'disabled' : '' }}></textarea>
            @elseif ($field['type'] === 'file')
                <input type="file" class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" {{ $field['required'] ?? false ? 'required' : '' }} {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
            @elseif ($field['type'] === 'password')
                <input type="password" class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" placeholder="{{ $field['placeholder'] ?? ''}}" {{ $field['required'] ?? false ? 'required' : '' }} {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
            @elseif ($field['type'] === 'email')
                <input type="email" class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" placeholder="{{ $field['placeholder'] ?? '' }}" {{ $field['required'] ?? false ? 'required' : '' }} {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
            @elseif ($field['type'] === 'number')
                <input type="number" class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" placeholder="{{ $field['placeholder'] ?? '' }}" {{ $field['required'] ?? false ? 'required' : '' }} {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
            @elseif ($field['type'] === 'date')
                <input type="date" class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" placeholder="{{ $field['placeholder'] ?? '' }}" {{ $field['required'] ?? false ? 'required' : '' }} {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
            @elseif ($field['type'] === 'select')
                <select class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" {{ $field['required'] ?? false ? 'required' : '' }}>
                    <option value="" disabled selected>{{ $field['placeholder'] ?? '' }}</option>
                    @foreach ($field['options'] as $value => $label)
                        <option value="{{ $value }}" {{ old($field['field'], $field['default'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            @elseif ($field['type'] === 'checkbox')
            <div class="row ms-3 mt-2">
                @foreach ($field['options'] as $value => $label)
                
                    <div class="col-md-3 form-check">
                        <input class="form-check-input" type="checkbox" id="{{ $field['field'] }}-{{ $value }}" name="{{ $field['field'] }}[]" value="{{ $value }}" {{ in_array($value, old($field['field'], [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ $field['field'] }}-{{ $value }}">
                            {{ $label }}
                        </label>
                    </div>
                
                @endforeach
            </div>
            @else
                <input type="text" class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" placeholder="{{ $field['placeholder'] ?? '' }}" {{ $field['required'] ?? false ? 'required' : '' }} {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
            @endif
            <span class="text-danger" id="{{ $field['field'] }}Error"></span>
        </div>
    @endforeach
                </div>
                    <div class="col-sm-12 mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-blue" id="saveBtn" value="create">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
