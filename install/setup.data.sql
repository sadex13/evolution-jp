# MODx Database Script for New/Upgrade Installations
#
# Each sql command is separated by double lines


#
# Dumping data for table `site_content`
#


REPLACE INTO `{PREFIX}site_content` VALUES (1, 'document', 'text/html', 'Home', 'Welcome to MODX', 'Introduction to MODX', 'index', '', 1, 0, 0, 0, 0, 'Create and do amazing things with MODX', '<h3>MODXへようこそ!</h3>\r\n<p>\r\nこのサンプルサイトが個性的なウェブサイトを構築するためのヒントになれば幸いです。\r\n</p>\r\n<ul>\r\n	<li><strong>新着記事の一覧</strong><br />\r\n	「Ditto」スニペットを使って、任意階層の記事一覧を出力できます。簡易のブログに利用するのもよいでしょう。 <a href=\"[~2~]\">新着情報を見る</a></li>\r\n	<li><strong>コメント機能</strong><br />\r\n	サイトの登録ユーザーがあなたの記事にコメントすることができます。 <a href=\"[~9~]\">表示例</a></li>\r\n	<li><strong>RSSフィード</strong><br />\r\n	同じく「Ditto」スニペットを使って、RSSフィードを設置できます。 <a href=\"[(site_url)][~11~]\">RSSフィードを見る</a></li>\r\n	<li><strong>QuickManager（クイックマネージャー）</strong><br />\r\n	管理画面にログインしている状態なら、実際に表示されているページを見ながらダイレクトに編集できます。 <a href=\"[~14~]\">コンテンツ管理をもっと見る</a></li>\r\n	<li><strong>強力なナビゲーション生成機能</strong><br />\r\n	「Wayfinder」スニペットを使って、ナビゲーションを自由自在に作ることができます。 <a href=\"[~22~]\">メニューについてもっと見る</a></li>\r\n	<li><strong>エラーページ(page not found[404])をカスタマイズ</strong><br />\r\n	探し物をして迷子になった閲覧者を助けてあげてください。 <a href=\"[~7~]\">404ページを見る</a></li>\r\n	<li><strong>問い合わせフォーム</strong><br />\r\n	「eForm」スニペットを使って、問い合わせフォームを設置することができます。 <a href=\"[~6~]\">問い合わせフォームを見る</a></li>\r\n</ul>\r\n<p>\r\n<strong><a href=\"manager\">MODXの管理画面([(site_url)]manager/)</a>はこちらです。</strong>\r\n</p>', 1, 2, 1, 1, 1, 1, 1144904400, 1, 1160262629, 0, 0, 0, 0, 0, 'Home', 0, 0, 0, 0, 0, 0, 0);


REPLACE INTO `{PREFIX}site_content` VALUES (2, 'document', 'text/html', '新着情報', '新着情報の一覧', '', 'news', '', 1, 0, 0, 0, 1, '', '<style type=\"text/css\">\r\n  span.keyword {background-color:#9ba8b1;color:#fff;padding:2px;}\r\n  td.date {width:120px;}\r\n</style>\r\n<table>\r\n[[Ditto? &parents=`2` &tpl=`ditto_news` &dateFormat=`%Y年%-m月%-d日`]]\r\n</table>\r\n', 0, 2, 2, 0, 0, 1, 1144904400, 1, 1159818696, 0, 0, 0, 0, 0, '新着情報', 0, 0, 0, 0, 0, 0, 0);


REPLACE INTO `{PREFIX}site_content` VALUES (6, 'document', 'text/html', 'お問い合わせ', 'お問い合わせ [(site_name)]', '', 'contact-us', '', 1, 0, 0, 0, 0, '', '[!eForm? &formid=`ContactForm` &tpl=`ContactForm` &report=`ContactFormReport` &subject=`[+subject+]` &to=`[(emailsender)]` &ccsender=`1` &gotoid=`46` !]\r\n', 0, 2, 14, 1, 0, 1, 1144904400, 1, 1159303922, 0, 0, 0, 0, 0, 'お問い合わせ', 0, 0, 0, 0, 0, 0, 0);


REPLACE INTO `{PREFIX}site_content` VALUES (7, 'document', 'text/html', '404 - Document Not Found', 'お探しのページが見当たりません (Page Not Found)', '', 'doc-not-found', '', 1, 0, 0, 0, 0, '', '<p>\r\n存在しないページへアクセスしたようです。 ログインするか、 以下のページにアクセスしてください:\r\n</p>\r\n<div>[[Wayfinder? &startId=`0` &showDescription=`1`]]</div>\r\n\r\n', 1, 2, 4, 0, 1, 1, 1144904400, 1, 1159301173, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 1);


REPLACE INTO `{PREFIX}site_content` VALUES (9, 'document', 'text/html', '新サービスのお知らせ', '新サービスのお知らせ', '', 'newservice', '', 1, 0, 0, 2, 0, '', '<p>新サービスのお知らせです。</p>\r\n', 1, 2, 0, 1, 1, -1, 1300505696, 1, 1300505697, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 1);


REPLACE INTO `{PREFIX}site_content` VALUES (11, 'document', 'application/rss+xml', 'RSS フィード', '[(site_name)] RSSフィード', '', 'feed.rss', '', 1, 0, 0, 0, 0, '', '[[Ditto? &parents=`2` &format=`rss` &display=`20` &total=`20` &removeChunk=`Comments`]]', 0, 0, 6, 0, 0, 1, 1144904400, 1, 1160062859, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 1);


REPLACE INTO `{PREFIX}site_content` VALUES (14, 'document', 'text/html', 'コンテンツ管理', 'コンテンツマネージメント', '', 'cms', '', 1, 0, 0, 15, 0, '', '<h3>管理画面からコンテンツ管理</h3>\r\n<p>MODXの管理画面は、機能豊富でデザインもスタイリッシュ。コンテンツを新規追加したり、テンプレートを調整したり、ウェブサイトを構成する各種パーツの管理も簡単にできます。ユーザグループごとに、管理画面の操作権限を設定することもできます。また、モジュールを追加して、他のデータセットと連動したり、管理業務を簡易化することも可能です。</p>\r\n<h3>ウェブページ側からコンテンツ管理</h3>\r\n<p>QuickManager（クイックマネージャー）を使えば、サイトをブラウザーで見ながら、ページの内容を編集できます。管理画面を経由せず、ほとんどのコンテンツ要素とテンプレート変数を手軽に編集できます。</p>\r\n<h3>ウェブユーザーに新規コンテンツの作成を許可できます。</h3>\r\n<p>特殊なデータ入力作業も、MODXのAPIを利用して投稿画面をカスタマイズすれば簡単にできるようになります。</p>', 0, 2, 3, 1, 1, 1, 1144904400, 1, 1158331927, 0, 0, 0, 0, 0, 'Manage Content', 0, 0, 0, 0, 0, 0, 0);


REPLACE INTO `{PREFIX}site_content` VALUES (15, 'document', 'text/html', 'MODXの主な機能', 'MODXの主な機能', '', 'features', '', 1, 0, 0, 0, 1, '', '[!Wayfinder?startId=`[*id*]`!]', 0, 2, 7, 1, 1, 1, 1144904400, 1, 1158452722, 0, 0, 0, 1144777367, 1, 'MODXの機能', 0, 0, 0, 0, 0, 0, 0);


REPLACE INTO `{PREFIX}site_content` VALUES (16, 'document', 'text/html', 'AjaxとWeb2.0', 'AjaxとWeb2.0', '', 'ajax', '', 1, 1159264800, 0, 15, 0, '', '<strong>Ajax技術との相性のよさ</strong>\r\n<p>\r\nコアが直接出力するhtmlコードがほとんどないMODXは、流行のAjaxテクニックを自由自在に扱うことができます。アクセシビリティの高い、正しいCSSレイアウトのサイト管理も簡単にできます。ウェブ標準に則ったサイト作成が簡単にできます。(もし必要なら、tableタグに依存したレイアウトも簡単です)\r\n</p>', 1, 2, 1, 1, 1, 1, 1144904400, 1, 1159307504, 0, 0, 0, 0, 0, 'AjaxとWeb2.0', 0, 0, 0, 0, 0, 0, 0);


REPLACE INTO `{PREFIX}site_content` VALUES (18, 'document', 'text/html', 'サイトをオープンしました。', 'サイトをオープンしました', '', 'begin', '', 1, 0, 0, 2, 0, '', '<p>サイトをオープンしました。MODXで作りました。</p>\r\n', 1, 2, 2, 1, 1, -1, 1299728096, 1, 1299728097, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 1);


REPLACE INTO `{PREFIX}site_content` VALUES (22, 'document', 'text/html', 'メニューとリスト', '自由度が高いメニューとリスト', '', 'menus', '', 1, 1159178400, 0, 15, 0, '', '<h3>Your documents - listed how you want them</h3>\r\n<p>\r\n汎用CMSの評価の要となるのが、ナビゲーションコントロールと複数コンテンツのリスト表示。MODXでは、これらのコンテンツコントロールを2つの高機能スニペットに託しました。それがDitto（ディットー）とWayfinder（ウェイファインダー）です。\r\n</p>\r\n<h3>Wayfinder - メニュー生成スニペット</h3>\r\n<p>どのような種類のメニューでも実現します。このサイトでは、Wayfinderはドロップダウンメニューの生成に用いられていますが、他のどんなタイプのメニューやサイトマップも生成可能です。</p>\r\n<h3>Ditto（ディトゥー - 文章のリストアップスニペット）</h3>\r\n<p>新着情報の一覧を生成したり、サイトマップを作ったり、テンプレート変数との組み合わせで関連文書をリストアップしたり、RSSフィードの生成を行ったりします。Wayfinderとは異なるアプローチでナビゲーションを作ることもできます。このサイトでは、新着情報の記事一覧の表示に使われています。</p>\r\n<h3>カスタマイズは無限に可能</h3>\r\n<p>\r\nDittoとWayfinderのオプション、テンプレートを使用しても、満足のいくデザインや効果が得られない場合、独自の処理を作ることもできますし、<a href=\"http://modx.com/extras.html\">MODXのリポジトリ</a>から他のスニペットを探すこともできます。MODXのメニュータイトル、要約、メニューの場所、そのほか諸々は、APIを利用することによって思いどおりのデザインを作ることができます。\r\n</p>', 1, 2, 2, 1, 1, 1, 1144904400, 1, 1160148522, 0, 0, 0, 0, 0, 'メニューとリスト', 0, 0, 0, 0, 0, 0, 0);


REPLACE INTO `{PREFIX}site_content` VALUES (24, 'document', 'text/html', '拡張性豊かなデザインワーク', '拡張性豊かなデザインワーク', '', 'extendable', '', 1, 1159092732, 0, 15, 0, '', '<p>\r\nMODXコミュニティでは、イメージギャラリーやeコマース、その他様々なアドオン部品が <a href=\"http://modxcms.com/extras.html\">リポジトリ</a> で配布されてます。\r\n</p>\r\n<h3>テンプレート変数はデータバインディングが可能</h3>\r\n<p>\r\n「テンプレート変数」は、高機能なカスタムフィールドです。単なるテキストの入力項目ではなく、プログラムと連動した高度なコントロールが可能です。ここでは、コードの実行結果やデータソースによって異なる情報を返す特殊な例をご紹介します。ここではログインメニューを「@バインディング」で実現する例を示します。次のフィールドを追加することでログイン状態に従ってメニューの表示内容を変化させることができます。:\r\n<code>@EVAL if ($modx-&gt;getLoginUserID()) return \'ログアウト\'; else return \'ログイン\';</code>\r\n</p>\r\n<h3>カスタムフォーム</h3>\r\n<p>\r\nカスタムフォームとの関連性を示すために、ウェブユーザー登録システムとログインシステムの呼び出し方法をカスタマイズしてあります。\r\n</p>\r\n<h2>その他</h2>\r\n<h3>\r\n<strong>スマートな概要表示</strong></h3>\r\n<p>\r\n区切りたい位置に&quot;&lt;!-- splitter --&gt;&quot;というタグを入れることで、記事を途中で区切ることができます。また、OL, UL, DIVといった重要なタグが前後に分かれてもタグが閉じるように動作するためレイアウトが崩れることはありません。\r\n</p>', 1, 2, 4, 1, 1, 2, 1144904400, 1, 1159309971, 0, 0, 0, 0, 0, '思い通りの拡張', 0, 0, 0, 0, 0, 0, 0);


REPLACE INTO `{PREFIX}site_content` VALUES (33, 'document', 'text/html', 'サポート', 'サポート', '', 'geting-help', '', 1, 0, 0, 0, 0, '', '<p>\r\n<a href=\"http://modx.jp/\" target=\"_blank\">MODX開発チーム</a>はドキュメントの改良に努めています。:\r\n</p>\r\n<ul>\r\n	<li>MODXのテンプレート構築に関する基本的なノウハウについては、<a href=\"http://modx.jp/docs.html\" target=\"_blank\">デザイナーズガイドをご覧ください</a>。 </li>\r\n	<li>MODXを利用したコンテンツの編集方法については、<a href=\"http://modx.jp/docs.html\" target=\"_blank\">コンテンツエディターガイドをご覧ください</a>。 </li>\r\n	<li>管理ツールの詳細とユーザーやグループの設定については、<a href=\"http://modx.jp/docs.html\" target=\"_blank\">アドミニストレーションガイドを精読してください</a>。</li>\r\n	<a href=\"http://modx.jp/docs.html\" target=\"_blank\">デベロッパーズガイドで</a>MODXの構造とAPIについて記述しています。\r\n	<li>もし誰かがこのサイトをインストールしていて、それを見たあなた自身がMODXについて知りたくなったとしたら、<a href=\"http://modx.jp/docs.html\" target=\"_blank\">スタートガイドをご覧ください</a>。</li>\r\n</ul>\r\n<p>\r\nそして<a href=\"http://modx.jp/\" target=\"_blank\">MODXフォーラムを利用すれば、</a>いつでもノウハウを得たり、質疑応答ができます。 \r\n</p>', 1, 2, 8, 1, 1, 2, 1144904400, 2, 1144904400, 0, 0, 0, 0, 0, 'サポート', 0, 0, 0, 0, 0, 0, 0);


REPLACE INTO `{PREFIX}site_content` VALUES (46, 'document', 'text/html', 'ありがとうございます', '', '', 'thank-you', '', 1, 0, 0, 0, 0, '', '<h3>ありがとうございます!</h3>\r\n<p>\r\nお問い合わせを受け付けました。また、あなたのメールアドレスに送信内容のコピーが届いているはずです。\r\n</p>\r\n<p>\r\n内容をチェックし、お返事いたします。\r\n</p>', 1, 2, 13, 1, 1, 1, 1159302141, 1, 1159302892, 0, 0, 0, 1159302182, 1, '', 0, 0, 0, 0, 0, 0, 1);


#
# Dumping data for table `system_settings`
#


REPLACE INTO `{PREFIX}system_settings` VALUES('error_page', '7');


REPLACE INTO `{PREFIX}system_settings` VALUES('unauthorized_page', '4');


