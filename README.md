
<div align="center">
  <h1>Laravel N2Search</h1>

  <p>
    为Laravel设计的分词搜索工具。
  </p>


<!-- Badges -->
<p>
  <a target="_blank" href="https://ivone.me">
    <img src="https://img.shields.io/badge/Author-Ivone-green" alt="Author" />
  </a>
  <img src="https://img.shields.io/github/license/ivone-liu/laravel-search" />
  <img src="https://img.shields.io/badge/status-testing-red" />
</p>

  <h4>
    <a href="https://packagist.org/packages/ivone/n2search">Find Me at Packagist</a>
  <span> · </span>
    <a href="https://github.com/ivone-liu/laravel-search/issues">Report Bug</a>
  </h4>
</div>

<br />

<!-- About the Project -->
## 关于N2Search

Laravel为PHP提供了一个优雅使用的框架，无数的开发者为Laravel提供了非常多的插件。在我使用的过程中，发现Laravel并没有一个比较好用且灵活调整的搜索方法，无论是官方提供的Scout还是基于Scout延伸出来的其他搜索，要么是过重（如：Scout+ElasticSearch方案）要么是不够便捷（如：Scout不支持许多SQL语法），致使在查询中非常不方便。

所以，基于以上的问题，我用Jieba作为分词器，单独构建了一套存储在Redis中的分词索引，并且以链式操作的形式，重新构建整套的Laravel数据库链式操作，确保既能够有效分词，也能方便查询。

<!-- 特点 -->
## 特点

🌀 比Scout效率要高

👨‍💻 可实现中文的拼音搜索

⛓ 支持类似Laravel Eloquent ORM链式操作，无违和感

📊 自动队列支持

🎰 支持多字段查询

<!-- How To Use -->
## 如何使用

### 🔧 版本要求

`PHP` >= `7.0`

`Laravel` >= `8`

确保安装`Redis`

### 🛠 安装

建议通过Composer来安装

```shell
composer require ivone/n2search
```

### 📈 开始构建索引

你可以新建一个`Command`，用命令行形式把你的数据表重新构建成搜索索引。

```php
$count = Model::count();
$bar = $this->output->createProgressBar($count);
$bar->setEmptyBarCharacter(' ');
$bar->setProgressCharacter('>');
$bar->setBarCharacter('<comment>=</comment>');

$n2 = new N2Search();
$logs = NotesModel::get()->toArray();
foreach ($logs as $log) {
    $n2->load(Model::query(), ['content'])->add_one($log['id']);
    $bar->advance();
}

$bar->finish();
```

