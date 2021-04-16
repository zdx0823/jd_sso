<div style="
width: 520px;
margin: 0 auto;
margin-top: 20px;
border: 1px solid #ddd;
border-radius: 5px;
">
  <div style="
    height: 50px;
    line-height: 50px;
    text-align: center;
    font-size: 14px;
    color: #333;
    border-bottom: 1px solid #ddd;
  ">
    {{$msg}}
  </div>
  <div style="
    text-align: center;
    box-sizing: border;
    padding: 10px 20px;
  ">
      <a
        href="{{route($confirmRoute, $token)}}"
        style="
          color: #2563EB
        "
      >{{route($confirmRoute, $token)}}</a>
  </div>
  <div style="
    height: 50px;
    line-height: 50px;
    text-align: center;
    font-size: 14px;
    color: #666;
    border-top: 1px solid #ddd;
  ">
    如非您本人操作，请勿理会。
  </div>
</div>