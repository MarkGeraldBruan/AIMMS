<footer class="app-footer">
    <div class="footer-content">
        <div class="footer-left">
            <span class="footer-copyright">© 2025 The 4mulas | Universidad de Dagupan-BSIT | All Rights Reserved</span>
        </div>
        <div class="footer-right">
            <div class="footer-group-info">
                <span class="footer-group">The4mulas</span>
                <img src="{{ asset('assets/img/The4mulas.png') }}" alt="The4mulas Logo" class="footer-logo">
            </div>
        </div>
    </div>
</footer>

<style>
.app-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    border-top: 1px solid #388e3c;
    padding: 3px 0;
    background: #063b09ff;
    z-index: 1000;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0 auto;
    padding: 0 20px 0;
}

.footer-left {
    flex: 1;
    text-align: left; 
}

.footer-right {
    flex: 1;
    text-align: right;
}

.footer-group-info {
    display: flex;
    align-items: center;
    gap: 10px;
    justify-content: flex-end;
}

.footer-copyright {
    color: white;
    font-size: 14px;
    font-weight: bold;
}

.footer-logo {
    height: 40px;
    width: auto;
    max-width: 120px;
}

.footer-group {
    color: white;
    font-size: 16px;
    font-weight: bold;
}

@media (max-width: 768px) {
    .footer-content {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }

    .footer-left,
    .footer-right {
        flex: none;
    }

    .footer-group-info {
        justify-content: center;
    }

    .footer-logo {
        height: 30px;
    }
}
</style>
