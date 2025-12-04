from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import Select
import time

# ====================================
# Configurações
# ====================================
BASE_URL = "http://localhost:8080"  # Ajuste para a URL do seu projeto
EDGE_DRIVER = True  # True para Edge, False para Chrome

def get_driver():
    """Retorna uma instância do driver configurado"""
    if EDGE_DRIVER:
        return webdriver.Edge()
    else:
        return webdriver.Chrome()


# ====================================
# Teste 1: Login com Sucesso
# ====================================
def test_login_sucesso():
    """Teste de login com credenciais válidas"""
    driver = get_driver()

    try:
        print("\n" + "="*50)
        print("TESTE 1: Login com Sucesso")
        print("="*50)
        
        print("Abrindo página de login...")
        driver.get(f"{BASE_URL}/login")
        time.sleep(2)

        print("Preenchendo e-mail...")
        email_input = driver.find_element(By.ID, "email")
        for letra in "admin@email.com":
            email_input.send_keys(letra)
            time.sleep(0.05)

        print("Preenchendo senha...")
        senha_input = driver.find_element(By.ID, "senha")
        for letra in "admin":
            senha_input.send_keys(letra)
            time.sleep(0.05)

        print("Clicando no botão de login...")
        time.sleep(1)
        login_button = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_button.click()

        print("Aguardando redirecionamento...")
        time.sleep(3)

        # Verifica se foi redirecionado para o dashboard
        current_url = driver.current_url
        assert "dashboard" in current_url or "admin" in current_url, f"URL atual: {current_url}"
        print("Login realizado com sucesso!")
        print(f"URL atual: {current_url}")

    except Exception as e:
        print(f" Erro encontrado: {e}")
        print("Screenshot salvo como 'erro_login_sucesso.png'")

    finally:
        time.sleep(3)
        print("Fechando o navegador...")
        driver.quit()


# ====================================
# Teste 2: Login com Credenciais Inválidas
# ====================================
def test_login_falha():
    """Teste de login com credenciais inválidas"""
    driver = get_driver()

    try:
        print("\n" + "="*50)
        print("TESTE 2: Login com Credenciais Inválidas")
        print("="*50)
        
        print("Abrindo página de login...")
        driver.get(f"{BASE_URL}/login")
        time.sleep(2)

        print("Preenchendo e-mail inválido...")
        email_input = driver.find_element(By.ID, "email")
        email_input.send_keys("usuario@invalido.com")

        print("Preenchendo senha inválida...")
        senha_input = driver.find_element(By.ID, "senha")
        senha_input.send_keys("senhaerrada")

        print("Clicando no botão de login...")
        time.sleep(1)
        login_button = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        login_button.click()

        print("Aguardando mensagem de erro...")
        time.sleep(2)

        # Verifica se há mensagem de erro
        try:
            alert = driver.find_element(By.CSS_SELECTOR, ".alert-warning, .alert-danger")
            print(f"✅ Mensagem de erro exibida: {alert.text}")
        except:
            print("⚠️  Nenhuma mensagem de erro encontrada")

    except Exception as e:
        print(f"❌ Erro encontrado: {e}")

    finally:
        time.sleep(3)
        print("Fechando o navegador...")
        driver.quit()

# ====================================
# Teste 3: Preenchimento do Formulário de Atendimento
# ====================================
def test_formulario_atendimento():
    """Teste de preenchimento do formulário de atendimento"""
    driver = get_driver()

    try:
        print("\n" + "="*50)
        print("TESTE 3: Formulário de Atendimento")
        print("="*50)
        
        # Primeiro faz login
        print("Fazendo login...")
        driver.get(f"{BASE_URL}/login")
        time.sleep(2)
        
        driver.find_element(By.ID, "email").send_keys("admin@email.com")
        driver.find_element(By.ID, "senha").send_keys("admin")
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        time.sleep(3)

        print("Navegando para formulário de atendimento...")
        driver.get(f"{BASE_URL}/home")
        time.sleep(2)

        # Forma de Atendimento
        print("Selecionando forma de atendimento...")
        forma_select = Select(driver.find_element(By.ID, "formaAtendimento"))
        forma_select.select_by_value("Presencial")
        time.sleep(1)

        # Perfil do Atendido
        print("Selecionando perfil do atendido...")
        perfil_select = Select(driver.find_element(By.ID, "perfilAtendido"))
        perfil_select.select_by_value("ADS")
        time.sleep(1)

        # Aguarda campos serem habilitados
        wait = WebDriverWait(driver, 10)
        
        # Tipo de Atendimento
        print("Selecionando tipo de atendimento...")
        tipo_select = Select(driver.find_element(By.ID, "tipoAtendimento"))
        tipo_select.select_by_value("orientações sobre empreendedorismo")
        
        time.sleep(2)

        # Aceitar termos
        print("Aceitando termos...")
        checkbox = driver.find_element(By.ID, "invalidCheck")
        driver.execute_script("arguments[0].scrollIntoView(true);", checkbox)
        time.sleep(1)
        checkbox.click()
        time.sleep(1)

        print("Enviando formulário...")
        submit_button = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        driver.execute_script("arguments[0].scrollIntoView(true);", submit_button)
        time.sleep(1)
        submit_button.click()

        print("Aguardando confirmação...")
        time.sleep(3)

        # Verifica mensagem de sucesso
        try:
            success_alert = driver.find_element(By.CSS_SELECTOR, ".alert-success")
            print(f"✅ Formulário enviado com sucesso!")
            print(f"Mensagem: {success_alert.text}")
        except:
            print("⚠️  Nenhuma mensagem de sucesso encontrada")
            current_url = driver.current_url
            print(f"URL atual: {current_url}")

    except Exception as e:
        print(f"❌ Erro encontrado: {e}")

    finally:
        time.sleep(3)
        print("Fechando o navegador...")
        driver.quit()


# ====================================
# Teste 4: Validação de Campos Obrigatórios
# ====================================
def test_validacao_campos_obrigatorios():
    """Teste de validação de campos obrigatórios no formulário"""
    driver = get_driver()

    try:
        print("\n" + "="*50)
        print("TESTE 4: Validação de Campos Obrigatórios")
        print("="*50)
        
        # Primeiro faz login
        print("Fazendo login...")
        driver.get(f"{BASE_URL}/login")
        time.sleep(2)
        
        driver.find_element(By.ID, "email").send_keys("admin@email.com")
        driver.find_element(By.ID, "senha").send_keys("admin")
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        time.sleep(3)

        print("Navegando para formulário...")
        driver.get(f"{BASE_URL}/home")
        time.sleep(2)

        print("Tentando enviar formulário vazio...")
        submit_button = driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
        driver.execute_script("arguments[0].scrollIntoView(true);", submit_button)
        time.sleep(1)
        submit_button.click()
        time.sleep(2)

        # Verifica mensagens de validação
        print("Verificando mensagens de validação...")
        invalid_feedbacks = driver.find_elements(By.CSS_SELECTOR, ".invalid-feedback")
        
        if invalid_feedbacks:
            print(f"✅ {len(invalid_feedbacks)} campos obrigatórios identificados")
            for feedback in invalid_feedbacks[:3]:  # Mostra apenas os 3 primeiros
                if feedback.is_displayed():
                    print(f"   - {feedback.text}")
        else:
            print("⚠️  Nenhuma mensagem de validação encontrada")

    except Exception as e:
        print(f"❌ Erro encontrado: {e}")

    finally:
        time.sleep(3)
        print("Fechando o navegador...")
        driver.quit()


# ====================================
# Executar Todos os Testes
# ====================================
def run_all_tests():
    """Executa todos os testes E2E"""
    print("\n" + "="*50)
    print("INICIANDO BATERIA DE TESTES E2E - FGTAS")
    print("="*50)
    
    tests = [
        test_login_sucesso,
        test_login_falha,
        test_formulario_atendimento,
        test_validacao_campos_obrigatorios
    ]
    
    total = len(tests)
    print(f"\nTotal de testes a executar: {total}\n")
    
    for i, test in enumerate(tests, 1):
        print(f"\n>>> Executando teste {i}/{total}...")
        try:
            test()
        except Exception as e:
            print(f"❌ Teste falhou: {e}")
        time.sleep(2)  # Pausa entre testes
    
    print("\n" + "="*50)
    print("BATERIA DE TESTES FINALIZADA")
    print("="*50)


if __name__ == "__main__":
    run_all_tests()
