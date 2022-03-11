# GitHub Actions: CI/CD

> Использование GitHub как системы для непрерывной интеграции и развертывания современного веб-проекта

## Запуск

Для первого запуска достаточно поднять контейнеры:

```shell
docker-compose up -d
```

**Обязательно поменяйте пароль!**

Логин: `admin`

Пароль: `LecOaToQQNm0cWIMIf`

## Фиксация изменений

Воспользуйтесь скриптом `utils/dump.php`.

Скрип получает дамп базы данных с помощью *mysqldump* контейнера и сохраняет его по пути `dump/dump.sql` (относительно
корня проекта); при развёртывании этот дамп автоматически загружается в контейнер.

При миграции на другие ОС могут возникнуть проблемы с выводом в консоль и TTY. WSL2 сразу же отдаёт весь вывод в STDOUT,
мимо скрипта, поэтому дамп оказывается пуст; если перенаправить вывод, слетит кодировка. Поэтому **в Windows надо делать
дамп именно из консоли Windows (CMD, PowerShell)**.
