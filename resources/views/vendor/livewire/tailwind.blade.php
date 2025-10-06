@php
if (! isset($scrollTo)) {
$scrollTo = 'body';
}

$scrollIntoViewJsSnippet = "";
if ($scrollTo !== false) {
$scrollIntoViewJsSnippet = "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()";
}
@endphp

<div>
    @if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginacion Navegacion" class="flex items-center justify-between">
        {{-- Versión móvil --}}
        <div class="flex justify-between flex-1 sm:hidden">
            <span>
                @if ($paginator->onFirstPage())
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-md">
                    Anterior
                </span>
                @else
                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled"
                    dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-[#0285fc] bg-white border border-[#0285fc] rounded-md hover:bg-[#0285fc] hover:text-white focus:outline-none transition duration-150">
                    Anterior
                </button>
                @endif
            </span>

            <span>
                @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled"
                    dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before"
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-[#0285fc] bg-white border border-[#0285fc] rounded-md hover:bg-[#0285fc] hover:text-white focus:outline-none transition duration-150">
                    Siguiente
                </button>
                @else
                <span
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-md">
                    Siguiente
                </span>
                @endif
            </span>
        </div>

        {{-- Versión escritorio --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 leading-5">
                    Mostrando
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    a
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    de
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    resultados
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rtl:flex-row-reverse rounded-md shadow-sm">
                    {{-- Anterior --}}
                    <span>
                        @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Anterior"
                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-l-md">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after"
                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-[#0285fc] bg-white border border-[#0285fc] rounded-l-md hover:bg-[#0285fc] hover:text-white transition duration-150"
                            aria-label="Anterior">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        @endif
                    </span>

                    {{-- Páginas --}}
                    @foreach ($elements as $element)
                    @if (is_string($element))
                    <span
                        class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default leading-5">
                        {{ $element }}
                    </span>
                    @endif

                    @if (is_array($element))
                    @foreach ($element as $page => $url)
                    <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                        @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                            class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-[#0285fc] border border-[#0285fc] cursor-default leading-5">
                            {{ $page }}
                        </span>
                        @else
                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-[#0285fc] bg-white border border-[#0285fc] leading-5 hover:bg-[#0285fc] hover:text-white transition duration-150"
                            aria-label="Ir a la página {{ $page }}">
                            {{ $page }}
                        </button>
                        @endif
                    </span>
                    @endforeach
                    @endif
                    @endforeach

                    {{-- Siguiente --}}
                    <span>
                        @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')"
                            x-on:click="{{ $scrollIntoViewJsSnippet }}"
                            dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after"
                            class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-[#0285fc] bg-white border border-[#0285fc] rounded-r-md hover:bg-[#0285fc] hover:text-white transition duration-150"
                            aria-label="Siguiente">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        @else
                        <span aria-disabled="true"
                            class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-400 bg-white border border-gray-300 cursor-default rounded-r-md">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        @endif
                    </span>
                </span>
            </div>
        </div>
    </nav>
    @endif
</div>