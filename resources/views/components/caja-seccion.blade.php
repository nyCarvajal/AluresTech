@props(['titulo'])

<div class="card mb-4">
    <div class="card-header"><h4 class="mb-0">{{ $titulo }}</h4></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-condensed">
                {{ $tabla }}
            </table>
        </div>
    </div>
</div>
