<div class="footer">
    <div class="info">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="site-map">
                        @foreach(array_slice($mainMenu, 0, 6) as $menu)
                        <dl>
                            <dt>{{ $menu['name'] }}</dt>

                            @if(isset($menu['sub']))
                                @foreach($menu['sub'] as $sub)
                                <dd>
                                    <a href="{{ route('category.index', ['id' => $sub['id']]) }}">{{ $sub['name'] }}</a>
                                </dd>
                                @endforeach
                            @endif
                        </dl>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="service-phone">
                                <p class="phone"><a href="tel:4000000000">400-0000-000</a></p>
                                <p>24小时全天在线</p>
                                <p>在线客服<br />09:00 - 18:00</p>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="row">
                                <div class="col-lg-6 qr-code text-center">
                                    <p>微信二维码</p>
                                    <img class="img-responsive" src="/images/temp1.jpg" />
                                    <span>扫一扫 关注有礼</span>
                                </div>
                                <div class="col-lg-6 qr-code text-center">
                                    <p>微博二维码</p>
                                    <img class="img-responsive" src="/images/temp2.jpg" />
                                    <span>扫一扫 关注有礼</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 contacts">
                        <p><span>地址：武汉市</span><span>邮箱：<a href="mailto:buer2202@163.com">buer2202@163.com</a></span><span>服务热线：<a href="tel:4000000000">400-0000-000</a></span></p>
                        <p><span>Copyright &copy; 2014-2016 武汉XX公司版权所有</span><span>鄂ICP证：000000号</span></p>
                    </div>
                    <div class="col-lg-5 logo">
                        <img src="/images/logo.jpg" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function () {
    var $siteMap = $('.site-map'),
        $siteMapDl = $siteMap.find('dl'),
        widthSiteMap = $siteMap.width(),
        widthSiteMapDl = $siteMapDl.width();
    if(widthSiteMap == widthSiteMapDl) {
        $siteMapDl.css('float', 'left').width(widthSiteMap / $siteMapDl.length);
    }
})
</script>