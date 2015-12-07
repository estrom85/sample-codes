#ifndef CATEGORYTREEEDITABLE_H
#define CATEGORYTREEEDITABLE_H

#include "categorytree.h"
class CategoryTreeEditable : public CategoryTree
{
    Q_OBJECT
private:
    DBConnection *db;

protected:
    virtual QVariant getItemData(const QModelIndex &index) const;
public:
    CategoryTreeEditable(Item* root, DBConnection *db, QObject *parent = 0);

    virtual QVariant data(const QModelIndex &index, int role) const;
    virtual Qt::ItemFlags flags(const QModelIndex &index) const;
    virtual QVariant headerData(int section, Qt::Orientation orientation,
                             int role = Qt::DisplayRole) const;

    virtual int columnCount(const QModelIndex &parent = QModelIndex()) const;

    virtual bool setData(const QModelIndex &index, const QVariant &value, int role);
    virtual bool insertRows(int row, int count, const QModelIndex &parent);
    virtual bool removeRows(int row, int count, const QModelIndex &parent);

    virtual Qt::DropActions supportedDropActions();
signals:

public slots:

};

#endif // CATEGORYTREEEDITABLE_H
