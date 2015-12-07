/* 
 * File:   ViewModel.cpp
 * Author: mato
 * 
 * Created on Nedeľa, 2013, december 8, 13:29
 */

#include "ViewModel.h"

ViewModel::ViewModel() {
    package = 0;

}

ViewModel::~ViewModel() {
    if (package != 0)
        delete package;
    //  if(_prevDialog != 0)
    //        delete _prevDialog;
}

bool ViewModel::packageLoaded() {
    return package != 0;
}

void ViewModel::CreatePackage() {
    if (package != 0) {
        QMessageBox::StandardButton response = QMessageBox::question(0, tr("New Package"),
                tr("Package is still opened. Do you want to save changes first?"),
                QMessageBox::Yes | QMessageBox::No | QMessageBox::Cancel, QMessageBox::Cancel);
        if (response == QMessageBox::Yes){
            this->SavePackage();
        }else if(response == QMessageBox::Cancel){
            return;
        }
    }
    if (package != 0)
        delete package;
    package = new Package("temp");
    reset();
    emit PackageChanged(package);
}

void ViewModel::SavePackage() {
    if (package == 0) {
        QMessageBox msg;
        msg.setText("No package opened. Open the package or create new one.");
        msg.exec();
        return;
    }
    QString file = QFileDialog::getSaveFileName(0, "Save Package", "",
            tr("Package (*.pck)"));
    string filename = file.toStdString();
    int pos = filename.find_last_of(".");
    if(filename.substr(pos+1).compare("pck")!=0){
        filename+=".pck";
    }
    
    package->SavePackage(filename);
    emit PackageChanged(package);
}

void ViewModel::ExportFiles(){
    if (_selectedKeys.size() == 0) {
        QMessageBox msg;
        msg.setText("No file selected");
        msg.exec();
        return;
    }
    QString dir = QFileDialog::getExistingDirectory(0, tr("Export selected files"),
                                                 tr(""),
                                                 QFileDialog::ShowDirsOnly
                                                 | QFileDialog::DontResolveSymlinks);
    string path;
    FileHandler* file;
    path = dir.toStdString();
    int pos = path.find_last_of("/\\");
    char type;
    if(pos==string::npos){
        type = '/';
    }else{
        type = path[pos];
    }
    
    for (set<string>::iterator i = _selectedKeys.begin(); i != _selectedKeys.end(); i++) {
        string key = *i;
        file = (*package)[key];
     
        path = dir.toStdString() + type + file->getFileName();
        file->writeFileData(path);
    }
}

void ViewModel::LoadPackage() {
    if (package != 0) {
        QMessageBox::StandardButton response = QMessageBox::question(0, tr("Load Package"),
                tr("Package is still opened. Do you want to save changes first?"),
                QMessageBox::Yes | QMessageBox::No | QMessageBox::Cancel, QMessageBox::Cancel);
        if (response == QMessageBox::Yes){
            this->SavePackage();
            
        }else if(response == QMessageBox::Cancel){
            return;
        }
        delete package;
        package = 0;
    }
    QString file = QFileDialog::getOpenFileName(0, "Open Package", "",
            tr("Package (*.pck)"));
    string filename = file.toStdString();
    
    
    package = new Package(filename,"temp");
    emit PackageChanged(package);
    reset();
}

void ViewModel::AddFiles() {
    if (package == 0) {
        QMessageBox msg;
        msg.setText("No package opened. Open the package or create new one.");
        msg.exec();
        return;
    }
    QStringList files = QFileDialog::getOpenFileNames(0, "Open Files", "",
            tr("All Files (*) ;;Images (*.jpg *.png *.bmp *.gif *.tif);;Audio (*.mp3 *.ogg *.wav);;Fonts(*.ttf)"));
    for (QStringList::Iterator i = files.begin(); i != files.end(); i++) {

        package->LoadFile(i->toStdString());
    }
    reset();
}

void ViewModel::RemoveFiles() {
    if (_selectedKeys.size() == 0) {
        QMessageBox msg;
        msg.setText("No file selected");
        msg.exec();
        return;
    }

    for (set<string>::iterator i = _selectedKeys.begin(); i != _selectedKeys.end(); i++) {
       //cout << *i;
        package->RemoveFile(*i);
    }
    reset();
    _selectedKeys.clear();

}

void ViewModel::EditKey() {
    if (package == 0) {
        QMessageBox msg;
        msg.setText("Load or create package");
        msg.exec();
        return;
    }
    if (_selectedKeys.size() == 0) {
        QMessageBox msg;
        msg.setText("Select file.");
        msg.exec();
        return;
    }
    if (_selectedKeys.size() > 1) {
        QMessageBox msg;
        msg.setText("Key has to be unique. Please select only one file");
        msg.exec();
        return;
    }
    string key = *(_selectedKeys.begin());
    bool ok;
    QString newkey = QInputDialog::getText(0, "Change key", "Enter new Key", QLineEdit::Normal, "", &ok);
    if (!ok || newkey.isEmpty())
        return;
    package->ChangeKey(key, newkey.toStdString());
    reset();
    _selectedKeys.clear();
}

QString ViewModel::getPackagePath() {
    //if(package == 0)
    //return QString("test");
    if (package == 0)
        return QString();
    return QString(package->getFileName().c_str());
}

void ViewModel::setSelectedItems(QModelIndexList indexes) {
    _selectedKeys.clear();
    for (QModelIndexList::Iterator i = indexes.begin(); i != indexes.end(); i++) {
        _selectedKeys.insert(((QModelIndex) (*i)).data(Qt::DisplayRole).toString().toStdString());
    }
}

int ViewModel::rowCount(const QModelIndex& parent) const {
    if (package == 0)
        return 0;
    return package->NumberOfFiles();
}

int ViewModel::columnCount(const QModelIndex& parent) const {
    return 4;
}

QVariant ViewModel::data(const QModelIndex& index, int role) const {
    if (role != Qt::DisplayRole && role !=Qt::ToolTipRole)
        return QVariant();
    FileHandler* file;
    file = const_cast<FileHandler*> ((*package)[index.row()]);
    
    if(role == Qt::ToolTipRole){
        return QVariant(file->getFileName().c_str());
    }
    
    switch (index.column()) {
        case 0:
            return QVariant(file->getKey().c_str());
        case 1:
            switch (file->getDataType()) {
                case FileHandler::DATA_TYPE_AUDIO:
                    return QVariant("Audio");
                case FileHandler::DATA_TYPE_FONT:
                    return QVariant("Font");
                case FileHandler::DATA_TYPE_IMAGE:
                    return QVariant("Image");
                default:
                    return QVariant("Data");
            }
        case 2:
            return QVariant(file->getFileName().c_str());
        case 3:
            return QVariant(formatFileLength(file->getFileLength()));
            //return QVariant(file->getFileLength());
    }
    return QVariant();
}

QVariant ViewModel::headerData(int section, Qt::Orientation orientation, int role) const {
    if (orientation == Qt::Vertical)
        return QVariant(section);
    if (role != Qt::DisplayRole)
        return QVariant();

    switch (section) {
        case 0:
            return QVariant("Key");
        case 1:
            return QVariant("Type");
        case 2:
            return QVariant("Filename");
        case 3:
            return QVariant("Size");
    }
    return QVariant();
}

QString ViewModel::formatFileLength(unsigned int length) const {
    unsigned int power = 0;
    unsigned int base = 1;
    while (base < length) {
        power++;
        base *= 2;
    }
    double var = round(100 * ((double) length / base)) / 100.0;
    QString out;
    if (power % 10 == 0) {
        if (var < 0.5) {
            power -= 10;
        }
    } else {
        power -= power % 10;
    }
    base = 1 << power;
    var = round(100 * ((double) length / base)) / 100.0;
    out += QVariant(var).toString();
    switch (power) {
        case 0:
            out += QString("B");
            break;
        case 10:
            out += QString("kB");
            break;
        case 20:
            out += QString("MB");
            break;
        case 30:
            out += QString("GB");
            break;
        case 40:
            out += QString("TB");
            break;
    }
    return out;
}
