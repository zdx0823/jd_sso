<div class="{{$class}}">
  <div class="w-full flex">

    <div class="
    bg-white 
    text-gray-600
      flex-1
      h-12
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
        name="{{$name}}"
        placeholder="{{$placeholder}}"
        rule="{{$rule}}"
        jshook="{{$jshook}}"

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
  
    {{ isset($code) ? $code : '' }}

  </div>
  <p class="hidden text-sm text-red-600 px-3 w-full truncate">
    <i class="iconfont iconjinggao1 relative" style="top: 1px"></i>
    <span>邮箱格式错误</span>
  </p>
</div>