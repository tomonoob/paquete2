var raiz = document.documentElement;
var pantallaCarga = document.getElementById('pantalla-carga');
var reducirMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function ocultarCarga() {
    if (pantallaCarga == null) {
        return;
    }

    pantallaCarga.classList.add('oculta');

    setTimeout(function () {
        raiz.classList.remove('con-carga');
    }, 380);
}

if (raiz.classList.contains('con-carga')) {
    try {
        sessionStorage.setItem('bf_carga', '1');
    } catch (error) {
    }

    if (reducirMovimiento) {
        setTimeout(ocultarCarga, 250);
    } else {
        setTimeout(ocultarCarga, 1150);
    }
}

var saldo = document.getElementById('saldo');
if (saldo != null && !reducirMovimiento) {
    var meta = Number(saldo.getAttribute('data-saldo'));

    if (meta > 0) {
        var actual = 0;
        var paso = meta / 24;
        var reloj = setInterval(function () {
            actual = actual + paso;
            if (actual >= meta) {
                actual = meta;
                clearInterval(reloj);
            }
            saldo.textContent = '$ ' + Math.round(actual).toLocaleString('es-CO');
        }, 30);
    }
}

var botonesVer = document.querySelectorAll('.ver-clave');
for (var i = 0; i < botonesVer.length; i++) {
    botonesVer[i].onclick = function () {
        var campo = document.getElementById(this.getAttribute('data-campo'));
        if (campo == null) {
            return;
        }

        if (campo.type == 'password') {
            campo.type = 'text';
            this.textContent = 'Ocultar';
        } else {
            campo.type = 'password';
            this.textContent = 'Mostrar';
        }
    };
}

var botonDemo = document.getElementById('usar-demo');
if (botonDemo != null) {
    botonDemo.onclick = function () {
        var usuario = document.getElementById('usuario');
        var clave = document.getElementById('clave');

        if (usuario == null || clave == null) {
            return;
        }

        usuario.value = this.getAttribute('data-usuario');
        clave.value = this.getAttribute('data-clave');
    };
}

var chips = document.querySelectorAll('.chip-valor');
for (var j = 0; j < chips.length; j++) {
    chips[j].onclick = function () {
        var campo = document.getElementById('valor');
        if (campo == null) {
            return;
        }

        campo.value = this.getAttribute('data-valor');

        for (var k = 0; k < chips.length; k++) {
            chips[k].classList.remove('elegido');
        }

        this.classList.add('elegido');
    };
}

var camposNumero = document.querySelectorAll('input[inputmode="numeric"]');
for (var m = 0; m < camposNumero.length; m++) {
    camposNumero[m].oninput = function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    };
}

var formularios = document.querySelectorAll('form');
for (var f = 0; f < formularios.length; f++) {
    formularios[f].onsubmit = function () {
        var boton = this.querySelector('button[type="submit"]');

        if (boton != null) {
            boton.disabled = true;
            boton.textContent = 'Procesando…';
        }
    };
}
