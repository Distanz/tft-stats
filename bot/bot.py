import discord
from discord.ext import commands
from discord import app_commands

import mysql.connector
import bcrypt

from aumentos import todos_los_aumentos
from db_config import db_config

from datetime import datetime

# Intents y bot
intents = discord.Intents.default()
intents.message_content = True

bot = commands.Bot(command_prefix='!', intents=intents)
tree = bot.tree

# Base de datos
try:
    db = mysql.connector.connect(**db_config)
except mysql.connector.Error as err:
    print(f"❌ Error al conectar a la base de datos: {err}")
    exit()

#Definir el parch en el que se está trabajando
def obtener_parche_actual():
    ahora = datetime.now()

    parches = [
        ("15.1", datetime(2025, 7, 30, 4), datetime(2025, 8, 13, 3, 59)),
        ("15.2", datetime(2025, 8, 13, 4), datetime(2025, 8, 27, 3, 59)),
        ("15.3", datetime(2025, 8, 27, 4), datetime(2025, 9, 10, 3, 59)),
        ("15.4", datetime(2025, 9, 10, 4), datetime(2025, 9, 24, 3, 59)),
        ("15.5", datetime(2025, 9, 24, 4), datetime(2025, 10, 8, 3, 59)),
        ("15.6", datetime(2025, 10, 8, 4), datetime(2025, 10, 22, 3, 59)),
        ("15.7", datetime(2025, 10, 22, 4), datetime(2025, 11, 5, 3, 59)),
        ("15.8", datetime(2025, 11, 5, 4), datetime(2025, 11, 19, 3, 59)),
    ]

    for parche, inicio, fin in parches:
        if inicio <= ahora <= fin:
            return parche

    return "desconocido"

# ID del canal (se carga al iniciar)
canal_id = None

# Autocompletado de aumentos
async def autocomplete_augments(interaction: discord.Interaction, current: str):
    return [
        app_commands.Choice(name=a, value=a)
        for a in todos_los_aumentos if current.lower() in a.lower()
    ][:25]

# Slash command: /partida
@tree.command(name="partida", description="Registrar una partida de TFT")
@app_commands.describe(
    posicion="Tu posición (1 al 8)",
    composicion="Composición jugada",
    aumento_2_1="Aumento 2-1",
    aumento_3_2="Aumento 3-2",
    aumento_4_2="Aumento 4-2"
)
@app_commands.autocomplete(
    aumento_2_1=autocomplete_augments,
    aumento_3_2=autocomplete_augments,
    aumento_4_2=autocomplete_augments,
)
async def partida(
    interaction: discord.Interaction,
    posicion: int,
    composicion: str,
    aumento_2_1: str,
    aumento_3_2: str,
    aumento_4_2: str
):
    try:
        discord_id = str(interaction.user.id)
        discord_username = str(interaction.user)

        cursor = db.cursor()

        # Verificar usuario
        cursor.execute("SELECT id FROM usuarios WHERE discord_id = %s", (discord_id,))
        resultado = cursor.fetchone()

        if not resultado:
            password_hash = bcrypt.hashpw(discord_username.encode('utf-8'), bcrypt.gensalt()).decode('utf-8')
            cursor.execute("""
                INSERT INTO usuarios (username, password, rol, discord_id)
                VALUES (%s, %s, %s, %s)
            """, (discord_username, password_hash, 'user', discord_id))
            db.commit()
            user_id = cursor.lastrowid
        else:
            user_id = resultado[0]

        # Obtener el parche actual
        parche = obtener_parche_actual()

        # Insertar partida con parche
        cursor.execute("""
            INSERT INTO partidas (
                user_id, posicion, composicion,
                aumento_2_1, aumento_3_2, aumento_4_2, parche
            ) VALUES (%s, %s, %s, %s, %s, %s, %s)
        """, (user_id, posicion, composicion, aumento_2_1, aumento_3_2, aumento_4_2, parche))
        db.commit()

        await interaction.response.send_message("✅ Partida registrada con éxito.", ephemeral=True)
    except Exception as e:
        print(f"Error en /partida: {e}")
        await interaction.response.send_message("❌ Error al registrar la partida.", ephemeral=True)
    finally:
        cursor.close()


# Comando setup
@bot.command()
async def setup(ctx):
    global canal_id
    canal_id = ctx.channel.id
    with open("canal_id.txt", "w") as f:
        f.write(str(canal_id))
    await ctx.send(f"✅ Canal de registro configurado: <#{canal_id}>")

# Comando test
@bot.command()
async def test(ctx):
    await ctx.send("¡Hola! Estoy conectado y funcionando.")

# Evento: on_ready
@bot.event
async def on_ready():
    global canal_id
    await tree.sync()
    print(f"✅ Bot conectado como {bot.user}")

    # Leer canal_id
    try:
        with open("canal_id.txt") as f:
            canal_id = int(f.read().strip())
    except FileNotFoundError:
        print("⚠️ No se encontró canal_id.txt. Ejecuta !setup para configurarlo.")
        return
    except ValueError:
        print("⚠️ canal_id.txt está vacío o mal escrito.")
        return

    canal = bot.get_channel(canal_id)
    if canal is None:
        print("❌ No se encontró el canal.")
        return

    print("🔍 Buscando mensajes sin procesar...")

    async for mensaje in canal.history(limit=100):
        if mensaje.author.bot:
            continue
        if mensaje.content.startswith("!partida"):
            ya_reaccionado = any(r.emoji in ["✅", "❌"] for r in mensaje.reactions)
            if not ya_reaccionado:
                ctx = await bot.get_context(mensaje)
                await bot.invoke(ctx)

#Leer token y ejecutar bot
with open("bot_token.txt") as f:
    token = f.read().strip()

bot.run(token)
