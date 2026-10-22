<div align="center">

# 🔐 PasteVault

**Мини-pastebin на чистом PHP + SQLite**

Быстро поделиться кодом или текстом по ссылке. Без фреймворков, одна зависимость — PHP.

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white)](https://php.net)
[![SQLite](https://img.shields.io/badge/SQLite-3-003B57?logo=sqlite&logoColor=white)](https://sqlite.org)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

</div>

---

## Что умеет

- 📝 создание пасты с заголовком и выбором языка
- 🔗 ссылка на пасту + ссылка на «сырой» текст
- ⏰ паста сама удалится через час, день, неделю или месяц
- 🗑 ручное удаление пасты
- 🧹 очистка истёкших паст по cron
- 🎨 подсветка синтаксиса, тёмная тема, работает на телефонах

## Быстрый старт

Нужен только PHP 8.1+ (дополнительно ничего ставить не надо).

```bash
git clone https://github.com/thesl33py/PasteVault.git
cd PasteVault
php -S localhost:8000 router.php
```

Открыть → http://localhost:8000

База (`database/pastevault.sqlite`) создастся сама при первом запуске.

## Как устроено

```
public/index.php     — вход, маршруты
app/Core/            — Router, Database, Csrf
app/Controllers/     — обработчики
app/Models/          — сущность Paste
app/Services/        — логика (создание, поиск, удаление)
app/Views/           — шаблоны
bin/cleanup.php      — удаление истёкших паст
bin/seed.php         — добавляет пару паст-примеров
```

## Маршруты

| Что | Путь |
|---|---|
| Главная / создать | `/` |
| Показать пасту | `/paste/{id}` |
| Сырой текст | `/raw/{id}` |
| Удалить | `/paste/{id}/delete` |

## Очистка по расписанию

```cron
0 * * * * php /path/to/PasteVault/bin/cleanup.php
```

## Лицензия

MIT — см. [LICENSE](LICENSE)


<img width="1058" height="541" alt="image" src="https://github.com/user-attachments/assets/c53d4c99-c520-4b9b-8857-5b9254107c9d" />

