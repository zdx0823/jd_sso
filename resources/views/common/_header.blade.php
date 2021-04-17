<div class="w-full h-32 border-b">
  
  <div class="flex flex-row items-center justify-between w-full h-full md:w-9/12 mx-auto">
    <a href="{{route('indexPage')}}" class="logo"></a>
    
    @if (isset($type) && $type === 'regiest')
        
      <p class="text-sm self-end">
        已有账号？
        <a href="javascript:;" class="text-red-600 hover:underline hover:text-red-700">请登录 > </a>
      </p>
      
    @endif
  </div>

</div>