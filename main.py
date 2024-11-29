from telegram import InlineKeyboardButton, InlineKeyboardMarkup, InlineQueryResultArticle, InputTextMessageContent, Update
from telegram.ext import Application, InlineQueryHandler, CommandHandler, CallbackContext

# Токен вашего бота
TOKEN = '7693761118:AAGFkxdAhXSfnX6cbdI7egD1RUlEczEd9Dk'

# URL Mini App, который будет открываться по кнопке
MINI_APP_URL = 'https://t.me/FAbricaFP_bot?start=mini_app'

# Функция для обработки инлайн-запросов
async def inline_query_handler(update: Update, context: CallbackContext):
    query = update.inline_query.query
    results = []
    
    # Добавляем кнопку для открытия Mini App
    keyboard = [
        [InlineKeyboardButton("Перейти в магазин", url= "https://t.me/FAbricaFP_bot?start=mini_app")],  # URL вашего miniapp
        
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)  # Ответ с inline клавиатурой

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
async def start(update: Update, context: CallbackContext):
        # Создание inline кнопки с miniapp
    keyboard = [
        [InlineKeyboardButton("Открыть MiniApp", web_app={"url": "https://7acb-91-77-161-155.ngrok-free.app"})],  # URL вашего miniapp
        
    ]
    reply_markup = InlineKeyboardMarkup(keyboard)  # Ответ с inline клавиатурой
    
    # Отправка сообщения с inline кнопкой
    await update.message.reply_text('Нажмите на кнопку, чтобы открыть наш магазин или начать:', reply_markup=reply_markup)

# Основная функция для настройки и запуска бота
def main():
    application = Application.builder().token(TOKEN).build()
    
    # Регистрируем обработчики команд и инлайн-запросов
    application.add_handler(CommandHandler('start', start))
    application.add_handler(InlineQueryHandler(inline_query_handler))

    # Запуск бота
    application.run_polling()

if __name__ == '__main__':
    main()
