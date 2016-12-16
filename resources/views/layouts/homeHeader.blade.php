<header>
    <nav class="navbar">
        <div class="container">
            <div class="row">
                <h2 class="col-lg-6 logo">
                    <a href="/">{{ config('app.name') }}</a>
                </h2>
                <div class="col-lg-4 col-lg-offset-2">
                    <form class="search-bar">
                        <div class="input-group">
                              <input type="text" id="word" class="form-control" placeholder="请输入关键词">
                              <span class="input-group-btn">
                                    <a class="btn" href="http://www.baidu.com/s?word=" onclick="this.href+=document.getElementById('word').value" target="_blank">搜索</a>
                              </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="main-nav">
            <div class="container">
                <ul class="nav navbar-nav">
                    <li @if(isset($menuRootId) && $menuRootId === true) class="active" @endif)><a href="/">首页</a></li>
                    @foreach($mainMenu as $menu)
                    <li @if(isset($menuRootId) && $menu['id'] === $menuRootId) class="active" @endif><a href="{{ $menu['link'] ?: ('/category/' . $menu['id']) }}">{{ $menu['name'] }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>
</header>
