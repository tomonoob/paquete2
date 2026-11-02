var logo = document.getElementById('logo');
var destino = document.getElementById('destino-logo');
var pantallaCarga = document.getElementById('pantalla-carga');
var TAMANO = 120;
var logoEnEsquina = false;

function moverLogo(x, y, escala) {
    logo.style.transform = 'translate(' + x + 'px, ' + y + 'px) scale(' + escala + ')';
}

function logoAlCentro() {
    var x = window.innerWidth / 2 - TAMANO / 2;
    var y = window.innerHeight / 2 - TAMANO / 2 - 20;
    moverLogo(x, y, 1);
}

function logoALaEsquina() {
    var caja = destino.getBoundingClientRect();
    moverLogo(caja.left, caja.top, caja.width / TAMANO);
}

function contarSaldo() {
    var saldo = document.getElementById('saldo');
    if (saldo == null) {
        return;
    }

    var meta = Number(saldo.dataset.saldo);
    var actual = 0;
    var reloj = setInterval(function () {
        actual = actual + meta / 30;
        if (actual >= meta) {
            actual = meta;
            clearInterval(reloj);
        }
        saldo.textContent = '$ ' + Math.round(actual).toLocaleString('es-CO');
    }, 35);
}

function mostrarPagina() {
    logoEnEsquina = true;
    logoALaEsquina();
    logo.classList.add('pequeno');
    pantallaCarga.classList.add('oculta');
    document.body.classList.add('listo');
    contarSaldo();
}

if (document.documentElement.classList.contains('sin-loader')) {
    mostrarPagina();
} else {
    logoAlCentro();
    setTimeout(function () {
        logo.style.transition = 'transform 1s';
        mostrarPagina();
    }, 2500);
}

window.onresize = function () {
    logo.style.transition = 'none';
    if (logoEnEsquina) {
        logoALaEsquina();
    } else {
        logoAlCentro();
    }
};

var botonVer = document.getElementById('ver-clave');
if (botonVer != null) {
    botonVer.onclick = function () {
        var campo = document.getElementById('clave');
        if (campo.type == 'password') {
            campo.type = 'text';
            botonVer.textContent = 'Ocultar';
        } else {
            campo.type = 'password';
            botonVer.textContent = 'Mostrar';
        }
    };
}

var botonDemo = document.getElementById('usar-demo');
if (botonDemo != null) {
    botonDemo.onclick = function () {
        document.getElementById('usuario').value = botonDemo.dataset.usuario;
        document.getElementById('clave').value = botonDemo.dataset.clave;
    };
}
