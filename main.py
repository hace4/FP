from telegram import InlineKeyboardButton, InlineKeyboardMarkup, InlineQueryResultArticle, InputTextMessageContent, Update
from telegram.ext import Application, InlineQueryHandler, CommandHandler, CallbackContext

# Токен вашего бота
TOKEN = '7693761118:AAE147f6yxKx2MNd90BiBHccv05N_P_SB9k'

# URL Mini App, который будет открываться по кнопке
MINI_APP_URL = 'https://t.me/FAbricaFP_bot?start=mini_app'

# ID канала для отправки сообщения
CHANNEL_ID = '-1002216844212'

# Функция для обработки инлайн-запросов
async def inline_query_handler(update: Update, context: CallbackContext):
    query = update.inline_query.query
    results = []
    
    # Добавляем кнопку для открытия Mini App
    keyboard = [
        [InlineKeyboardButton("Магазин🛒", url=MINI_APP_URL), InlineKeyboardButton("Отзывы", url="https://t.me/+jhhFUi7OrNE0ZDYy")],
        
        #[InlineKeyboardButton("Открыть MiniApp", web_app={"url": "https://7acb-91-77-161-155.ngrok-free.app"})],
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)

    # Создаем результат, который будет отображаться в инлайн-режиме
    results.append(
        InlineQueryResultArticle(
            id="1",
            title="Магазин",
            input_message_content=InputTextMessageContent("Нажмите кнопку ниже, чтобы перейти в магазин."), 
            reply_markup=reply_markup
        )
    )

    # Отправляем результаты пользователю
    await update.inline_query.answer(results)

# Функция для старта бота
# Функция для старта бота
async def start(update: Update, context: CallbackContext):
    # Клавиатура с кнопкой для запуска мини-приложения
    keyboard = [
        [InlineKeyboardButton("Открыть магазин", web_app={"url": "https://fabricapara.shop/"})],
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)
    
    # Отправляем сообщение с кнопкой
    await update.message.reply_text(
        'Добро пожаловать! Нажмите на кнопку ниже, чтобы открыть приложение:',
        reply_markup=reply_markup
    )
# Функция для отправки сообщения в канал
async def send_message_to_channel(update: Update, context: CallbackContext):
        keyboard = [
        [InlineKeyboardButton("Магазин🛒", url=MINI_APP_URL), InlineKeyboardButton("Отзывы☑️", url="https://t.me/+jhhFUi7OrNE0ZDYy")],
        ]
        reply_markup = InlineKeyboardMarkup(keyboard)

        try:
            # Отправляем сообщение с клавиатурой в канал
            await context.bot.send_message(
                chat_id=CHANNEL_ID,
                text="Полный каталог товаров и отзывы вы найдёте по кнопкам ниже",
                reply_markup=reply_markup
        )
            await update.message.reply_text("Сообщение с клавиатурой отправлено в канал!")
        except Exception as e:
            await update.message.reply_text(f"Не удалось отправить сообщение в канал: {e}")
async def premium_emojis(update: Update, context: CallbackContext):
    # Список эмодзи, доступных только с Telegram Premium
    premium_emojis = "💎✨🛍️🎨🚀"
    
    # Отправляем их в ответ
    await update.message.reply_text(f"Премиум эмодзи: {premium_emojis}")

# Основная функция для настройки и запуска бота
# Основная функция для настройки и запуска бота
def main():
    application = Application.builder().token(TOKEN).build()
    
    # Регистрируем обработчики команд и инлайн-запросов
    application.add_handler(CommandHandler('start', start))
    application.add_handler(InlineQueryHandler(inline_query_handler))
    application.add_handler(CommandHandler('sendtoc', send_message_to_channel))
    application.add_handler(CommandHandler('premium_emojis', premium_emojis))  # Регистрируем команду для получения премиум эмодзи

    # Запуск бота
    application.run_polling()

if __name__ == '__main__':
    main()


if __name__ == '__main__':
    main()
