<?php

###############################################################################
# japanese_utf-8.php
# This is the English language page for the Geeklog Ban Plug-in!
#
# Copyright (C) 2003 Tom Willett
# twillett@users.sourceforge.net
#
# Japanese language file provided by mystralkk@gmail.com
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
###############################################################################

## $Id: 

###############################################################################
# Array Format: 
# $LANGXX[YY]:	$LANG - variable name
#		  	XX - file id number
#			YY - phrase id number
###############################################################################

/**
* General language
*/

$LANG_BAN00 = array (
    'ban'           => 'Ban(禁止)',
    'type'          => 'タイプ',
    'data'          => 'データ',
	'id'            => 'ID',
	'status'        => 'ステータス',
	'created'       => '作成日',
	'ttl'           => '生存期間',
	'note'          => '備考',
    'desc'          => '不正IPやボットをあなたのウェブサイトから締め出します。',
    'instructions'  => 'ボットや人々をあなたのウェブサイトから締め出すことができます。IPの数値(REMOTE_ADDRを使用)やリファラー(HTTP_REFERERを使用)、ユーザーエージェント(HTTP_USER_AGENTを使用)、ページ(SCRIPT_NAME)ごとに禁止を指定できます。パターンを一致させるには、大文字小文字を区別しない正規表現(preg_match)を使用してください。REMOTE_ADDR_RANGEを用いて、IPアドレスの範囲を、REMOTE_ADDR_CIDRを用いてCIDR表記を、あるいは単純なfrom-toの範囲を追加できます。詳しくは、Banプラグインの<a href="' . $_CONF['site_admin_url'] . '/plugins/ban/readme.html" target="_blank">Read Me</a>をご覧ください。<br' . XHTML . '><br' . XHTML . '>GUSプラグインを用いて自動禁止機能を使用しているなら、GUSプラグインがBanプラグインよりも先に読み込まれるようにプラグインエディターで設定してください。',
    'instructions_sfs_not_updated'  => '<br' . XHTML . '><br' . XHTML . '>Stop Forum Spam (SFS)データベースが更新されていません。手動で更新してください。', 
    'instructions_sfs'              => '<br' . XHTML . '><br' . XHTML . '>前回Stop Forum Spam (SFS)データベースを更新したのは%sです。
                                        <br' . XHTML . '><br' . XHTML . '>次回手動更新が可能になるのは、%s (現在時刻: %s)です。',
    'instructions_sfs_auto_on'      => '<br' . XHTML . '><br' . XHTML . '>次回Stop Forum Spam (SFS) databaseが自動でアップロードされるのは: %s',
    'instructions_sfs_auto_off'     => '<br' . XHTML . '><br' . XHTML . '>Stop Forum Spam (SFS)データベースの自動ダウンロードは現在、無効になっています。',
    'instructions_sfs_size'         => '<br' . XHTML . '><br' . XHTML . '>現在のStop Forum Spam (SFS)データベースのサイズは %s KBで有効なようです。ファイルサイズは普通、2000KB以上です。',
    'instructions_sfs_size_small'   => '<br' . XHTML . '><br' . XHTML . '><em>警告</em>: 現在のStop Forum Spam (SFS)データベースのサイズは %s KBです。ファイルサイズは普通、2000KB以上です。データベースが有効であることを確認した方が良いでしょう。',
    'instructions_sfs_size_error'   => '<br' . XHTML . '><br' . XHTML . '><em>警告</em>: 現在のStop Forum Spam (SFS)データベースのサイズを判断できませんでした。ファイルが破損している可能性があります。SFSデータベースをダウンロードし直すか、有効なIPが禁止対象にならないようにこの機能を無効にすることをお勧めします。',
    'instructions_sfs_db_missing'   => '<br' . XHTML . '><br' . XHTML . '><em>警告</em>: Stop Forum Spamは有効ですが、データベースのファイルがこのサーバー上に見つかりません。データベースのファイルをダウンロードするか、Stop Forum Spamを用いた禁止機能を無効にしてください。',
    'instructions_info' => '<br' . XHTML . '><br' . XHTML . '>参考のため、あなたのブラウザー情報は<br' . XHTML . '><br' . XHTML . '>HTTP_USER_AGENT: %s<br' . XHTML . '><br' . XHTML . '>REMOTE_ADDR: %s<br' . XHTML . '><br' . XHTML . '>HTTP_REFERER: %s<br' . XHTML . '><br' . XHTML . '>SCRIPT_NAME: %s<br' . XHTML . '><br' . XHTML . '>です。',
    'not_available' => '利用不可',
    'save'          => '保存',
    'cancel'        => 'キャンセル',
    'delete'        => '削除',
    'edit'          => '編集',
    'ban_editor'    => 'Banエディター',
    'ban_list'      => 'Banリスト',
    'log_viewer'    => 'ログビューワー',
    'download_sfs'  => 'SFSデータベースをダウンロード',

    'status_normal'         => '正常',
    'status_ttl_short'      => '生存期間(短)',
    'status_ttl_medium'     => '生存期間(中)',
    'status_ttl_long'       => '生存期間(長)',
    'status_white'          => 'ホワイト',
    
    'ban_plugin_note'		=> 'プラグイン%sが禁止', 
    
    'ban_invalid_logins_note'		=> '無効ログインの最大数に達しました。ユーザーID: %s', // specify id since user cannot change that
    
    'ip_auto_ban'           => 'IP自動禁止',
    
    'gus_user_agent_note'   => "GUSユーザーエージェントがIPに対して過去{$_BAN_CONF['gus_user_agent_time']}秒以内に({$_BAN_CONF['gus_user_agent_num']})を超えました。",
    'gus_hits_note'         => "GUSヒット数がIPに対して過去{$_BAN_CONF['gus_hits_time']}秒以内に({$_BAN_CONF['gus_hits_num']})を超えました。",
    'gus_referrer_note'     => "GUSリファラーがIPに対して過去{$_BAN_CONF['gus_referrer_time']}秒以内に({$_BAN_CONF['gus_referrer_num']})回数を超えてマッチしました。",
    'gus_url_note'          => "GUSページとクエリー文字列のリクエストがIPに対して過去{$_BAN_CONF['gus_url_time']}以内に({$_BAN_CONF['gus_url_num']})回数を超えてマッチしました。",
    
    'stopforumspam_note'   => "Stop Forum Spamデータベースが{$_BAN_CONF['stopforumspam_file_date']}日分古くなっています。新しいデータベースを<a href='http://www.stopforumspam.com/downloads' target='_blank'>Stop Forum Spamのウェブサイト</a>でダウンロードしてください。今すぐ自動でダウンロードするには、<a href='/admin/plugins/ban/index.php?mode=sfs_download'>ここをクリック</a>してください。自動ダウンロードが機能するには、SFSデータベースファイルに対して書き込め可能でなければなりません。",
    
    'error_editor_no_data'  => '禁止対象には、タイプ・ステータス・データが必要です。タイプとデータの組み合わせは、データベースに保存されているものと重複しないようにしてください。',
    'access_denied'         => 'アクセス拒否',
	'access_denied_msg'     => 'RootユーザーかBanユーザーしかこのページにはアクセスできません。あなたのユーザー名とIPを記録しました。'
);

###############################################################################
# Messages for COM_showMessage the submission form

$PLG_ban_MESSAGE1 = "保存しました。";
$PLG_ban_MESSAGE2 = '削除しました。';
$PLG_ban_MESSAGE3 = '選択項目を削除しました。';
$PLG_ban_MESSAGE4 = 'Stop Forum Spamのデータベースを更新しました。';
$PLG_ban_MESSAGE5 = 'Stop Forum Spamのデータベースをダウンロード・解凍する際に問題が発生しました。設定によっては、8時間毎に1回しかダウンロードできません。詳細はエラーログをご覧ください。';
