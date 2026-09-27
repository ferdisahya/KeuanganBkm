@if ($paginator->hasPages())
  <div class="custom-pagination-wrap" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; padding: 16px 24px; border-top: 1px solid #e2e8f0; background: #ffffff; border-radius: 0 0 16px 16px;">
    
    {{-- Pagination Summary --}}
    <div style="font-size: 13px; color: #64748b; font-weight: 500;">
      Menampilkan <span style="font-weight: 700; color: #1e293b;">{{ number_format($paginator->firstItem()) }}</span> 
      sampai <span style="font-weight: 700; color: #1e293b;">{{ number_format($paginator->lastItem()) }}</span> 
      dari <span style="font-weight: 700; color: #7a0f1d;">{{ number_format($paginator->total()) }}</span> total data
    </div>

    {{-- Pagination Buttons --}}
    <div style="display: flex; align-items: center; gap: 6px;">
      
      {{-- Previous Page Link --}}
      @if ($paginator->onFirstPage())
        <span style="display: inline-flex; align-items: center; justify-content: center; height: 36px; padding: 0 14px; border-radius: 10px; background: #f1f5f9; color: #94a3b8; font-size: 13px; font-weight: 600; cursor: not-allowed;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 4px;"><polyline points="15 18 9 12 15 6"/></svg>
          Sebelumnya
        </span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}" style="display: inline-flex; align-items: center; justify-content: center; height: 36px; padding: 0 14px; border-radius: 10px; background: #ffffff; border: 1.5px solid #e2e8f0; color: #1e293b; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='#dfbb6d'; this.style.color='#7a0f1d'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#1e293b'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 4px;"><polyline points="15 18 9 12 15 6"/></svg>
          Sebelumnya
        </a>
      @endif

      {{-- Page Numbers --}}
      @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
          <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; color: #94a3b8; font-size: 13px; font-weight: 600;">{{ $element }}</span>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #7a0f1d 0%, #3d030a 100%); color: #ffffff; font-size: 13px; font-weight: 700; border: 1px solid rgba(223, 187, 109, 0.4); box-shadow: 0 4px 10px rgba(122, 15, 29, 0.2);">{{ $page }}</span>
            @else
              <a href="{{ $url }}" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; background: #ffffff; border: 1.5px solid #e2e8f0; color: #1e293b; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='#dfbb6d'; this.style.color='#7a0f1d'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#1e293b'">{{ $page }}</a>
            @endif
          @endforeach
        @endif
      @endforeach

      {{-- Next Page Link --}}
      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="display: inline-flex; align-items: center; justify-content: center; height: 36px; padding: 0 14px; border-radius: 10px; background: #ffffff; border: 1.5px solid #e2e8f0; color: #1e293b; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='#dfbb6d'; this.style.color='#7a0f1d'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#1e293b'">
          Berikutnya
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left: 4px;"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
      @else
        <span style="display: inline-flex; align-items: center; justify-content: center; height: 36px; padding: 0 14px; border-radius: 10px; background: #f1f5f9; color: #94a3b8; font-size: 13px; font-weight: 600; cursor: not-allowed;">
          Berikutnya
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left: 4px;"><polyline points="9 18 15 12 9 6"/></svg>
        </span>
      @endif

    </div>
  </div>
@endif
