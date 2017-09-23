@if ($paginator->lastPage() > 1)
<ul class="pagination">
    <li class="{{ ($paginator->currentPage() == 1) ? ' disabled' : '' }}">
        <a href="{{ Asset('/'.env("PREFIX_ADMIN_PORTAL").'/manager-id?page=1')  }}"><</a>
    </li>
    @for ($i = 1; $i <= $paginator->lastPage(); $i++)
        <li class="{{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
            <a href="{{ Asset('/'.env("PREFIX_ADMIN_PORTAL").'/manager-id?page='.$i) }}">{{ $i }}</a>
        </li>
    @endfor
    <li class="{{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}">
        <a href="{{ Asset('/'.env("PREFIX_ADMIN_PORTAL").'/manager-id?page='.( $paginator->currentPage()+1))  }}" >></a>
    </li>
</ul>
@endif