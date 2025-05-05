{{-- 
    $id
    $size : modal size
    $title
    $slot : inputs
 --}}

<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}Label"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered {{ $size ?? 'modal-lg' }}" role="document">
        <div class="modal-content">
            <form id="{{ $id }}Form" method="POST">
                @csrf
                @isset($method)
                    @method($method)
                @else
                    @method('POST')
                @endisset

                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- Added horizontal line for separation --}}
                <hr class="m-0">

                <div class="modal-body">
                    {{ $slot }}
                </div>

                <div class="modal-footer">
                    <div class="d-flex justify-content-end w-100">
                        <button type="button" class="btn btn-sm btn-secondary mr-2" data-dismiss="modal">Close</button>

                        @isset($submitButton)
                            <button type="submit" class="btn btn-sm btn-primary" id="{{ $submitButton }}">Submit</button>
                        @endisset
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
