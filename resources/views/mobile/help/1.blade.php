@extends('mobile/layout')

@section('title', '如何邀请员工关注企业号')

@section('content')
    <article class="weui-article">
        <h1>邀请关注方式</h1>
        <section>
            <section>
                <h3>你可以通过以下三种方式让你的成员找到企业号</h3>
                <p>
                    <img src="/dist/img/help/1.jpg" />
                </p>
            </section>
            <section>
                <h2>方式一：二维码邀请</h2>
                <p>
                    通过【企业号管理后台 -> 设置 -> 基本信息】找到二维码之后，下发给成员关注。
                </p>
                <p>
                    <img src="/dist/img/help/2.png" />
                </p>
            </section>
            <section>
                <h2>方式二：转发名片关注</h2>
                <p>
                    企业号管理员登录企业号后台，设置企业名片后，成员收到企业号的文章内的头像之后，找到企业名片，将企业名片转发给其他同事，其他人即可通过企业名片关注。
                </p>
                <p>
                    <img src="/dist/img/help/3.png" />
                </p>
            </section>
            <section>
                <h2>方式三：搜索关注</h2>
                <p>
                    搜索企业号名称关注：【企业号 -> 设置 -> 功能设置 -> 企业号搜索】开启搜索之后，成员可以通过企业号全称搜索关注。
                </p>
                <p>
                    <img src="/dist/img/help/4.png" />
                </p>
            </section>
        </section>
    </article>
@endsection