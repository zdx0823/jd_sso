@if ($isSubmit)

  <input
  type="submit"
  value="{{$value}}"
  class="
    {{$bgColor}}
    {{$focusBgColor}}
    px-3 py-1
    rounded-sm
    text-centen align-middle text-white
    transition-colors duration-200 ease-in-out
    cursor-pointer
    focus:outline-none
    {{$class}}
  " />

@else

  <input
  type="button"
  value="{{$value}}"
  class="
    {{$bgColor}}
    {{$focusBgColor}}
    px-3 py-1
    rounded-sm
    text-centen align-middle text-white
    transition-colors duration-200 ease-in-out
    cursor-pointer
    focus:outline-none
    {{$class}}
  " />

@endif

