/****************************************************************************
** Meta object code from reading C++ file 'ViewModel.h'
**
** Created: Sat Dec 14 21:37:32 2013
**      by: The Qt Meta Object Compiler version 63 (Qt 4.8.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "ViewModel/ViewModel.h"
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'ViewModel.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 63
#error "This file was generated using the moc from 4.8.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
static const uint qt_meta_data_ViewModel[] = {

 // content:
       6,       // revision
       0,       // classname
       0,    0, // classinfo
       9,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       2,       // signalCount

 // signals: signature, parameters, type, tag, flags
      11,   10,   10,   10, 0x05,
      36,   10,   10,   10, 0x05,

 // slots: signature, parameters, type, tag, flags
      54,   10,   10,   10, 0x0a,
      70,   10,   10,   10, 0x0a,
      84,   10,   10,   10, 0x0a,
      98,   10,   10,   10, 0x0a,
     109,   10,   10,   10, 0x0a,
     123,   10,   10,   10, 0x0a,
     137,   10,   10,   10, 0x0a,

       0        // eod
};

static const char qt_meta_stringdata_ViewModel[] = {
    "ViewModel\0\0PackageChanged(Package*)\0"
    "FileListChanged()\0CreatePackage()\0"
    "SavePackage()\0LoadPackage()\0AddFiles()\0"
    "ExportFiles()\0RemoveFiles()\0EditKey()\0"
};

void ViewModel::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        Q_ASSERT(staticMetaObject.cast(_o));
        ViewModel *_t = static_cast<ViewModel *>(_o);
        switch (_id) {
        case 0: _t->PackageChanged((*reinterpret_cast< Package*(*)>(_a[1]))); break;
        case 1: _t->FileListChanged(); break;
        case 2: _t->CreatePackage(); break;
        case 3: _t->SavePackage(); break;
        case 4: _t->LoadPackage(); break;
        case 5: _t->AddFiles(); break;
        case 6: _t->ExportFiles(); break;
        case 7: _t->RemoveFiles(); break;
        case 8: _t->EditKey(); break;
        default: ;
        }
    }
}

const QMetaObjectExtraData ViewModel::staticMetaObjectExtraData = {
    0,  qt_static_metacall 
};

const QMetaObject ViewModel::staticMetaObject = {
    { &QAbstractTableModel::staticMetaObject, qt_meta_stringdata_ViewModel,
      qt_meta_data_ViewModel, &staticMetaObjectExtraData }
};

#ifdef Q_NO_DATA_RELOCATION
const QMetaObject &ViewModel::getStaticMetaObject() { return staticMetaObject; }
#endif //Q_NO_DATA_RELOCATION

const QMetaObject *ViewModel::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->metaObject : &staticMetaObject;
}

void *ViewModel::qt_metacast(const char *_clname)
{
    if (!_clname) return 0;
    if (!strcmp(_clname, qt_meta_stringdata_ViewModel))
        return static_cast<void*>(const_cast< ViewModel*>(this));
    return QAbstractTableModel::qt_metacast(_clname);
}

int ViewModel::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QAbstractTableModel::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 9)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 9;
    }
    return _id;
}

// SIGNAL 0
void ViewModel::PackageChanged(Package * _t1)
{
    void *_a[] = { 0, const_cast<void*>(reinterpret_cast<const void*>(&_t1)) };
    QMetaObject::activate(this, &staticMetaObject, 0, _a);
}

// SIGNAL 1
void ViewModel::FileListChanged()
{
    QMetaObject::activate(this, &staticMetaObject, 1, 0);
}
QT_END_MOC_NAMESPACE
