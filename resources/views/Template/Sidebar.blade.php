<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="light">
    <div class="sidebar-brand" style="background-color: rgb(91, 209, 123)">
        <a href="./index.html" class="brand-link">
            <img src="./public/img/KynovaPharma.png" alt="AdminLTE Logo" class="brand-image shadow" />
            <span class="brand-text fw-bold">Kynova Pharma</span>
        </a>
    </div>
    <div class="sidebar-wrapper" style="background-color: rgb(192, 231, 202)">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                aria-label="Main navigation" data-accordion="false" id="navigation">
                <li class="nav-item @if($menu == 'Dashboard') menu-open @endif">
                    <a href="#" class="nav-link ">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>
                            Dashboard
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('indexdashboard')}}" class="nav-link @if($menu == 'Dashboard') active @endif">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Dashboard Apotek </p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-header">KASIR</li>
                <li class="nav-item">
                    <a href="{{ route('indexkasir')}}"
                        class="nav-link @if ($menu == 'kasir') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Kasir</p></h3>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexmasterbarang')}}"
                        class="nav-link @if ($menu == 'logsesikasir') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Log Sesi Kasir</p> <span class="badge text-bg-danger" style="margin-left:4px">On Proses</span></h3>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexmasterbarang')}}"
                        class="nav-link @if ($menu == 'logtransaksikasir') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Log Transaksi Kasir</p> <span class="badge text-bg-danger" style="margin-left:4px">On Proses</span></h3>
                    </a>
                </li>
                   <li class="nav-item">
                    <a href=""
                        class="nav-link @if ($menu == 'riwayatpenjualan') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Riwayat Penjualan</p> <span class="badge text-bg-danger" style="margin-left:4px">On Proses</span></h3>
                    </a>
                </li>
                <li class="nav-item">
                    <a href=""
                        class="nav-link @if ($menu == 'logtransaksistok') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Log transaksi stok</p> <span class="badge text-bg-danger" style="margin-left:4px">On Proses</span></h3>
                    </a>
                </li>
                <li class="nav-header">GUDANG</li>
                <li class="nav-item">
                    <a href="{{ route('indexpurchaseorder')}}"
                        class="nav-link @if ($menu == 'datapurchaseorder') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Data Purchase Order</p></h3>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexstoksediaan')}}"
                        class="nav-link @if ($menu == 'stokpersediaan') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Stok Persediaan</p> </h3>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexlogkartustok')}}"
                        class="nav-link @if ($menu == 'logkartustok') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Log Kartu Stok</p></h3>
                    </a>
                </li>
                <li class="nav-header">LAPORAN</li>
                <li class="nav-item">
                    <a href="{{ route('indexmasterbarang')}}"
                        class="nav-link @if ($menu == 'transaksipenjualan') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Transaki Penjualan</p> <span class="badge text-bg-danger" style="margin-left:4px">On Proses</span></h3>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexmasterbarang')}}"
                        class="nav-link @if ($menu == 'datapenjualan') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Data Penjualan</p> <span class="badge text-bg-danger" style="margin-left:4px">On Proses</span></h3>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexmasterbarang')}}"
                        class="nav-link @if ($menu == 'laporanpurchaseorder') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Laporan Purhcase Order</p> <span class="badge text-bg-danger" style="margin-left:4px">On Proses</span></h3>
                    </a>
                </li>
                <li class="nav-header">DATA MASTER</li>
                <li class="nav-item">
                    <a href="{{ route('indexmasterbarang')}}"
                        class="nav-link @if ($menu == 'masterbarang') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Master Barang</p></h3>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('Indexmastersupplier')}}"
                        class="nav-link @if ($menu == 'mastersupplier') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Master Supplier</p></h3>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('Indexmasteruser')}}"
                        class="nav-link @if ($menu == 'masteruser') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Master User</p> <span class="badge text-bg-danger" style="margin-left:4px">On Proses</span></h3>
                    </a>
                </li>
                <li class="nav-header">INFO AKUN</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-person-vcard"></i>
                        <p class="text">Detail Akun</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="logout()">
                        <i class="nav-icon bi bi-box-arrow-left"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
