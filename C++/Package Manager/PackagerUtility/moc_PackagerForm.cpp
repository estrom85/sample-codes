/****************************************************************************
** Meta object code from reading C++ file 'PackagerForm.h'
**
** Created: Sat Dec 14 21:37:28 2013
**      by: The Qt Meta Object Compiler version 63 (Qt 4.8.1)
**
** WARNING! All changes made in this file will be lost!
*****************************************************************************/

#include "PackagerForm.h"
#if !defined(Q_MOC_OUTPUT_REVISION)
#error "The header file 'PackagerForm.h' doesn't include <QObject>."
#elif Q_MOC_OUTPUT_REVISION != 63
#error "This file was generated using the moc from 4.8.1. It"
#error "cannot be used with the include files from this version of Qt."
#error "(The moc has changed too much.)"
#endif

QT_BEGIN_MOC_NAMESPACE
static const uint qt_meta_data_PackagerForm[] = {

 // content:
       6,       // revision
       0,       // classname
       0,    0, // classinfo
       3,   14, // methods
       0,    0, // properties
       0,    0, // enums/sets
       0,    0, // constructors
       0,       // flags
       0,       // signalCount

 // slots: signature, parameters, type, tag, flags
      14,   13,   13,   13, 0x0a,
      25,   13,   13,   13, 0x0a,
      69,   49,   13,   13, 0x0a,

       0        // eod
};

static const char qt_meta_stringdata_PackagerForm[] = {
    "PackagerForm\0\0closeApp()\0"
    "packageLoaded(Package*)\0selected,deselected\0"
    "selectionChanged(QItemSelection,QItemSelection)\0"
};

void PackagerForm::qt_static_metacall(QObject *_o, QMetaObject::Call _c, int _id, void **_a)
{
    if (_c == QMetaObject::InvokeMetaMethod) {
        Q_ASSERT(staticMetaObject.cast(_o));
        PackagerForm *_t = static_cast<PackagerForm *>(_o);
        switch (_id) {
        case 0: _t->closeApp(); break;
        case 1: _t->packageLoaded((*reinterpret_cast< Package*(*)>(_a[1]))); break;
        case 2: _t->selectionChanged((*reinterpret_cast< const QItemSelection(*)>(_a[1])),(*reinterpret_cast< const QItemSelection(*)>(_a[2]))); break;
        default: ;
        }
    }
}

const QMetaObjectExtraData PackagerForm::staticMetaObjectExtraData = {
    0,  qt_static_metacall 
};

const QMetaObject PackagerForm::staticMetaObject = {
    { &QDialog::staticMetaObject, qt_meta_stringdata_PackagerForm,
      qt_meta_data_PackagerForm, &staticMetaObjectExtraData }
};

#ifdef Q_NO_DATA_RELOCATION
const QMetaObject &PackagerForm::getStaticMetaObject() { return staticMetaObject; }
#endif //Q_NO_DATA_RELOCATION

const QMetaObject *PackagerForm::metaObject() const
{
    return QObject::d_ptr->metaObject ? QObject::d_ptr->metaObject : &staticMetaObject;
}

void *PackagerForm::qt_metacast(const char *_clname)
{
    if (!_clname) return 0;
    if (!strcmp(_clname, qt_meta_stringdata_PackagerForm))
        return static_cast<void*>(const_cast< PackagerForm*>(this));
    return QDialog::qt_metacast(_clname);
}

int PackagerForm::qt_metacall(QMetaObject::Call _c, int _id, void **_a)
{
    _id = QDialog::qt_metacall(_c, _id, _a);
    if (_id < 0)
        return _id;
    if (_c == QMetaObject::InvokeMetaMethod) {
        if (_id < 3)
            qt_static_metacall(this, _c, _id, _a);
        _id -= 3;
    }
    return _id;
}
QT_END_MOC_NAMESPACE
