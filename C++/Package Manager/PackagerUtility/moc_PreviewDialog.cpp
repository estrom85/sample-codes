/****************************************************************************
** Meta object code from reading C++ file 'PreviewDialog.h'
**
** Created: Sun Dec 15 00:26:52 2013
**      by: The Qt Meta Object Compiler version 63 (Qt 4.8.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "PreviewDialog.h"
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'PreviewDialog.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 63
#error "This file was generated using the moc from 4.8.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
static const uint qt_meta_data_PreviewDialog[] = {

 // content:
       6,       // revision
       0,       // classname
       0,    0, // classinfo
       9,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       0,       // signalCount

 // slots: signature, parameters, type, tag, flags
      15,   14,   14,   14, 0x0a,
      45,   39,   14,   14, 0x0a,
      69,   14,   14,   14, 0x0a,
      82,   14,   14,   14, 0x08,
      99,   14,   14,   14, 0x08,
     127,   14,   14,   14, 0x08,
     149,   14,   14,   14, 0x08,
     163,   14,   14,   14, 0x08,
     170,   14,   14,   14, 0x08,

       0        // eod
};

static const char qt_meta_stringdata_PreviewDialog[] = {
    "PreviewDialog\0\0ChangePackage(Package*)\0"
    "index\0ShowDialog(QModelIndex)\0"
    "ShowDialog()\0SetTicks(qint64)\0"
    "stateChanged(Phonon::State)\0"
    "SetAudioPosition(int)\0CloseDialog()\0"
    "Prev()\0Next()\0"
};

void PreviewDialog::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        Q_ASSERT(staticMetaObject.cast(_o));
        PreviewDialog *_t = static_cast<PreviewDialog *>(_o);
        switch (_id) {
        case 0: _t->ChangePackage((*reinterpret_cast< Package*(*)>(_a[1]))); break;
        case 1: _t->ShowDialog((*reinterpret_cast< const QModelIndex(*)>(_a[1]))); break;
        case 2: _t->ShowDialog(); break;
        case 3: _t->SetTicks((*reinterpret_cast< qint64(*)>(_a[1]))); break;
        case 4: _t->stateChanged((*reinterpret_cast< Phonon::State(*)>(_a[1]))); break;
        case 5: _t->SetAudioPosition((*reinterpret_cast< int(*)>(_a[1]))); break;
        case 6: _t->CloseDialog(); break;
        case 7: _t->Prev(); break;
        case 8: _t->Next(); break;
        default: ;
        }
    }
}

const QMetaObjectExtraData PreviewDialog::staticMetaObjectExtraData = {
    0,  qt_static_metacall 
};

const QMetaObject PreviewDialog::staticMetaObject = {
    { &QDialog::staticMetaObject, qt_meta_stringdata_PreviewDialog,
      qt_meta_data_PreviewDialog, &staticMetaObjectExtraData }
};

#ifdef Q_NO_DATA_RELOCATION
const QMetaObject &PreviewDialog::getStaticMetaObject() { return staticMetaObject; }
#endif //Q_NO_DATA_RELOCATION

const QMetaObject *PreviewDialog::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->metaObject : &staticMetaObject;
}

void *PreviewDialog::qt_metacast(const char *_clname)
{
    if (!_clname) return 0;
    if (!strcmp(_clname, qt_meta_stringdata_PreviewDialog))
        return static_cast<void*>(const_cast< PreviewDialog*>(this));
    return QDialog::qt_metacast(_clname);
}

int PreviewDialog::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QDialog::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 9)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 9;
    }
    return _id;
}
QT_END_MOC_NAMESPACE
