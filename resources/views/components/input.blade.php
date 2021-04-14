<div class="
  w-full
  {{$class}}
">

  <div class="
  bg-white 
  text-gray-600
    w-full h-12
    border border-1
    rounded-md
    box-border
    flex
    overflow-hidden
  ">

    <div class="w-12 h-full flex justify-center">
      <i class="iconfont {{$icon}} self-center currentColor"></i>
    </div>

    <input 
      type="{{$type}}"
      placeholder="{{$placeholder}}"
      class="
        flex-1
        h-full
        box-border
        px-4 
        focus:text-gray-800
          focus:outline-none
      "
    >

  </div>
  <p class="hidden text-sm text-red-600 px-3 w-full truncate">
    <i class="iconfont iconjinggao1 relative" style="top: 1px"></i>
    邮箱格式错误
  </p>
</div>