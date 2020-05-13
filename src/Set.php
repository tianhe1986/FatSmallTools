<?php
namespace FatSmallTools;

class Color
{
    const RED = 1;
    const BLACK = 2;
}

// tree node
class TreeNode
{
    public $data;
    public $color;
    public $left; // left son
    public $right; // right son
    public $parent;
    
    public function __construct($data) {
        $this->data = $data;
        $this->color = Color::RED;
        $this->left = $this->right = $this->parent = null;
    }
}

class RedBlackTree
{
    private $root;
    
    private $size;
    
    protected $compareFun; // compare function
    
    public function __construct(callable $compareFun = null) {
        $this->root = null;
        $this->size = 0;
        $this->compareFun = $compareFun;
    }
    
    public function orderPrint()
    {
        $this->innerOrderPrint($this->root);
    }
    
    public function levelPrint()
    {
        $this->innerLevelPrint($this->root);
    }
    
    // for debug
    protected function innerOrderPrint(TreeNode $root = null)
    {
        if ($root === null) {
            return;
        }
        
        $this->innerOrderPrint($root->left);
        echo $root->data . "\n";
        $this->innerOrderPrint($root->right);
    }
    
    // for debug
    protected function innerLevelPrint(TreeNode $root = null)
    {
        if ($root === null) {
            return;
        }
        
        $queue = new \SplQueue();
        $queue->enqueue([$root, 0]);
        
        while ( ! $queue->isEmpty()) {
            $item = $queue->dequeue();
            
            $temp = $item[0];
            echo "level ".$item[1].", color ".$temp->color.", data".$temp->data."\n";
            
            if ($temp->left !== null) {
                $queue->enqueue([$temp->left, $item[1] + 1]);
            }
            
            if ($temp->right !== null) {
                $queue->enqueue([$temp->right, $item[1] + 1]);
            }
        }
    }
    
    protected function compare(TreeNode $nodea, TreeNode $nodeb)
    {
        if ($this->compareFun !== null) {
            return $this->compareFun($nodea->data, $nodeb->data);
        }
        
        // treat as number
        return $nodea->data - $nodeb->data;
    }


    protected function bstInsert(TreeNode $root = null, TreeNode $node = null)
    {
        // empty, then the new node become new root
        
        if (null === $root) {
            $this->size++;
            return $node;
        }
        
        if (null === $node) {
            return $root;
        }
        
        $compareResult = $this->compare($node, $root);
        
        if ($compareResult < 0 ) { // node is small, insert into left son tree
            $root->left = $this->bstInsert($root->left, $node);
            $root->left->parent = $root;
        } else if ($compareResult > 0) { // node is bigger, insert into right son tree
            $root->right = $this->bstInsert($root->right, $node);
            $root->right->parent = $root;
        }
        
        return $root;
    }
    
    protected function rotateLeft(TreeNode $node)
    {
        // right son, to be new root of this sub tree
        $rightSon = $node->right;
        
        $node->right = $rightSon->left;
        
        // deal parent of node
        $parent = $node->parent;
        if ($parent === null) { // no parent , must be root
            $this->root = $rightSon;
        } else if ($node == $parent->left) {
            $parent->left = $rightSon;
        } else {
            $parent->right = $rightSon;
        }
        
        $rightSon->parent = $node->parent;
        
        $node->parent = $rightSon;
        $rightSon->left = $node;
    }
    
    protected function rotateRight(TreeNode $node)
    {
        // similar deal as rotateLeft
        $leftSon = $node->left;
        
        $node->left = $leftSon->right;
        
        $parent = $node->parent;
        if ($parent === null) {
            $this->root = $leftSon;
        } else if ($node == $parent->left) {
            $parent->left = $leftSon;
        } else {
            $parent->right = $leftSon;
        }
        
        $leftSon->parent = $node->parent;
        
        $node->parent = $leftSon;
        $leftSon->right = $node;
    }
    
    protected function fixViolation(TreeNode $root, TreeNode $node)
    {
        // reference https://www.geeksforgeeks.org/red-black-tree-set-2-insert/
        
        while ($node != $root && $node->color == Color::RED && $node->parent->color == Color::RED) {
            $parentNode = $node->parent;
            $grandParentNode = $parentNode->parent;
            
            // parent is left child of grand-parent
            if ($parentNode == $grandParentNode->left) {
                $uncleNode = $grandParentNode->right;
                
                // uncle is also red， then grand-parent must be black
                // recoloring parent and uncle to black, grand-parent to red, continue fixing grand-parent
                if ($uncleNode !== null && $uncleNode->color == Color::RED) {
                    $uncleNode->color = Color::BLACK;
                    $parentNode->color = Color::BLACK;
                    $grandParentNode->color = Color::RED;
                    $node = $grandParentNode;
                } else {
                    // Left Right Case, left rotate parent, become Left Left Case
                    if ($node == $parentNode->right) {
                        $this->rotateLeft($parentNode);
                        $node = $parentNode;
                        $parentNode= $node->parent;
                    }
                    
                    // Left Left Case, right rotate grand-parent, then swap the color of parent and grand-parent
                    $this->rotateRight($grandParentNode);
                    $temp = $parentNode->color;
                    $parentNode->color = $grandParentNode->color;
                    $grandParentNode->color = $temp;
                    
                    $node = $parentNode;
                    // TODO: always break since parent must be BLACK ？
                }
            } else { // parent is right child of grand-parent
                $uncleNode = $grandParentNode->left;
                
                // uncle is also red
                if ($uncleNode !== null && $uncleNode->color == Color::RED) {
                    $uncleNode->color = Color::BLACK;
                    $parentNode->color = Color::BLACK;
                    $grandParentNode->color = Color::RED;
                    $node = $grandParentNode;
                } else {
                    // RIGHT LEFT case, right rotate parent, become RIGHT RIGHT case
                    if ($node == $parentNode->left) {
                        $this->rotateRight($parentNode);
                        $node = $parentNode;
                        $parentNode = $node->parent;
                    }
                    
                    // RIGHT RIGHT case, left rotate grand-parent, then swap the color of parent and grand-parent
                    $this->rotateLeft($grandParentNode);
                    
                    $temp = $parentNode->color;
                    $parentNode->color = $grandParentNode->color;
                    $grandParentNode->color = $temp;
                    
                    $node = $parentNode;
                }
            }
        }
        
        // always set root to BLACK
        $this->root->color = Color::BLACK;
    }
    
    public function insert($data)
    {
        $node = new TreeNode($data);
        
        // Do normal BST insert
        $this->root = $this->bstInsert($this->root, $node);
        
        // not insert
        if ($node->parent === null && $node != $this->root) {
            return;
        }

        // Fix Red Black Tree violations
        $this->fixViolation($this->root, $node);
    }
    
    public function erase($data)
    {
        
    }
    
    public function find($data)
    {
        
    }
}
